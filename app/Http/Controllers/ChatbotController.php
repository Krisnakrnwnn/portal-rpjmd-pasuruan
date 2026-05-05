<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\DocumentChunk;
use Barryvdh\DomPDF\Facade\Pdf;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string']);
        $userMessage = $request->input('message');
        $language = $request->input('language', 'id'); // Get language parameter
        $sessionId = $request->cookie('chat_session_id') ?? \Illuminate\Support\Str::uuid()->toString();
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json(['reply' => 'Sistem tidak terkonfigurasi (API Key hilang).'], 500);
        }

        try {
            // Save user message to database
            \App\Models\ChatMessage::create([
                'session_id' => $sessionId,
                'role' => 'user',
                'message' => $userMessage
            ]);
            
            // Ensure session exists
            \App\Models\ChatSession::firstOrCreate(
                ['session_id' => $sessionId],
                ['user_ip' => $request->ip()]
            );
            // STEP 1: Embed User Message with Retry Logic
            $retryCount = 0;
            $maxRetries = 3;
            $embedResponse = null;

            while ($retryCount < $maxRetries) {
                $embedResponse = Http::timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-embedding-001:embedContent?key={$apiKey}", [
                    'model' => 'models/gemini-embedding-001',
                    'content' => [
                        'parts' => [
                            ['text' => $userMessage]
                        ]
                    ]
                ]);

                if ($embedResponse->successful()) break;
                
                if ($embedResponse->status() === 429) {
                    $retryCount++;
                    sleep(2); // Wait 2 seconds before retrying
                } else {
                    break; // Other error
                }
            }

            if (!$embedResponse || !$embedResponse->successful()) {
                return response()->json(['reply' => 'Maaf, sistem sedang sangat sibuk memproses dokumen. Silakan coba sesaat lagi (Error Embedding).'], 500);
            }

            $questionEmbedding = $embedResponse->json('embedding.values');

            // If we have no documents ingested yet, fallback to regular chat
            $chunkCount = DocumentChunk::count();
            $contextText = "";

            if ($chunkCount > 0) {
                // STEP 2: Find top most similar chunks using Cosine Similarity in PHP
                // Menggunakan chunkById() untuk menghindari ERROR: canceling statement due to statement timeout
                $topChunks = [];
                
                DocumentChunk::chunkById(200, function ($chunks) use (&$topChunks, $questionEmbedding) {
                    foreach ($chunks as $chunk) {
                        if (!is_array($chunk->embedding)) {
                            continue;
                        }
                        
                        $score = $this->cosineSimilarity($questionEmbedding, $chunk->embedding);
                        
                        $topChunks[] = [
                            'score' => $score,
                            'text' => "[File: {$chunk->document_name}, Hal: {$chunk->page_number}]\n" . $chunk->chunk_text
                        ];
                    }
                    
                    // Urutkan dan potong tiap perulangan chunk agar array tidak membengkak di memori
                    usort($topChunks, fn($a, $b) => $b['score'] <=> $a['score']);
                    $topChunks = array_slice($topChunks, 0, 10);
                });
                $contextPieces = array_column($topChunks, 'text');
                
                // Prevent very low relevance (ubah threshold dari 0.4 ke 0.3 agar lebih toleran thd kecocokan frasa)
                if (count($topChunks) > 0 && $topChunks[0]['score'] > 0.3) {
                    $contextText = "Gunakan konteks dokumen RPJMD berikut untuk menjawab:\n\n" . implode("\n\n---\n\n", $contextPieces);
                }

            }

            // STEP 2B: Ambil Data SQL Langsung dari Tabel Website (Contoh: Berita)
            $webDataText = "";
            try {
                $latestNews = \App\Models\News::published()->latest()->take(3)->get();
                if ($latestNews->count() > 0) {
                    $webDataText .= "Info Tambahan dari Database Website (Berita Terbaru Kabupaten Pasuruan):\n";
                    foreach ($latestNews as $idx => $news) {
                        $tgl = $news->published_at ? $news->published_at->format('d M Y') : '-';
                        $webDataText .= ($idx + 1) . ". {$news->title} (Rilis: {$tgl})\n";
                    }
                }
            } catch (\Exception $e) {
                // Abaikan jika tabel tidak siap
            }

            // STEP 3: Prompt Gemini to answer (IMPROVED PROMPT)
            // Get appropriate greeting based on time
            $hour = now()->format('H');
            $greeting = 'Selamat pagi'; // Default 00:00 - 10:59
            if ($hour >= 11 && $hour < 15) {
                $greeting = 'Selamat siang';
            } elseif ($hour >= 15 && $hour < 18) {
                $greeting = 'Selamat sore';
            } elseif ($hour >= 18) {
                $greeting = 'Selamat malam';
            }
            
            // Get system instruction based on language
            $systemInstruction = $this->getSystemPrompt($language, $greeting);

            $prompt = $systemInstruction . "\n\n" . $contextText . "\n\n" . $webDataText . "\n\nPertanyaan Baru Warga: " . $userMessage;

            // STEP 4: Siapkan Riwayat Percakapan (Memory) agar bot tidak pikun
            // API Gemini butuh array "contents" berisi [{"role":"user/model", "parts":[{"text":"..."}]}]
            $history = session()->get('chatbot_history', []);
            $contents = [];
            
            // Masukkan memori percakapan murni masa lalu
            foreach ($history as $msg) {
                 $contents[] = $msg;
            }
            
            // Masukkan pertanyaan saat ini (telah dibumbui prompt konteks PDF & Berita SQL) ke indeks paling akhir
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $prompt]]
            ];

            // Kirim request ke Gemini dengan sistem percobaan ulang (Retry) jika server Google sibuk
            $retryG = 0;
            $chatResponse = null;
            
            while ($retryG < 3) {
                $chatResponse = Http::timeout(45)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => $contents
                ]);

                if ($chatResponse->successful()) break;
                
                // Jika error 503 (High Demand) atau 429 (Rate Limit), tunggu 3 detik lalu coba lagi
                if ($chatResponse->status() == 503 || $chatResponse->status() == 429) {
                    $retryG++;
                    sleep(3);
                } else {
                    break; // Error lain (berhenti mencoba)
                }
            }

            if ($chatResponse && $chatResponse->successful()) {
                $reply = $chatResponse->json('candidates.0.content.parts.0.text') ?? 'Saya tidak dapat memberikan jawaban saat ini.';
                
                // Save AI response to database
                \App\Models\ChatMessage::create([
                    'session_id' => $sessionId,
                    'role' => 'model',
                    'message' => $reply
                ]);
                
                // Simpan pertanyaan ASLI warga (tanpa prompt panjang biar bot tdk mabuk teks/kena token limit max)
                $history[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];
                // Simpan balasan text bot
                $history[] = ['role' => 'model', 'parts' => [['text' => $reply]]];
                
                // Batasi hanya ingat 4 pasang chat terakhir (8 elemen) agar irit size Session
                if (count($history) > 8) {
                    $history = array_slice($history, -8);
                }
                session()->put('chatbot_history', $history);
                
                // Format markdown if needed (Gemini returns markdown, can be processed client-side)
                return response()->json(['reply' => $reply])
                    ->cookie('chat_session_id', $sessionId, 43200); // 30 days
            }

            // Jika masih error setelah di-retry (Atau gagal gara-gara alasan lain)
            $errorData = $chatResponse ? $chatResponse->json() : null;
            $status = $chatResponse ? $chatResponse->status() : 500;
            
            if ($status == 503) {
                return response()->json(['reply' => '🙏 Mohon maaf, server AI kami saat ini sedang sangat antre (Server Overload). Silakan coba kirim ulang pertanyaan Anda dalam beberapa detik/menit ke depan.']);
            }
            
            if ($status == 429) {
                return response()->json(['reply' => '🚀 Wah, sepertinya Anda terlalu bersemangat bertanya! Sistem kami butuh waktu sejenak untuk bernapas. Mohon tunggu sekitar 1 menit sebelum mengirim pertanyaan lagi ya.']);
            }

            if ($status == 404) {
                return response()->json(['reply' => '🧩 Maaf, model AI tidak ditemukan atau endpoint salah (Error 404). Silakan hubungi admin untuk mengecek konfigurasi GEMINI_MODEL.']);
            }
            
            return response()->json(['reply' => "Terjadi kendala pada mesin AI (Kode $status). Silakan coba lagi nanti."], 500);

        } catch (\Exception $e) {
            return response()->json(['reply' => 'Sistem sedang sibuk atau terjadi kesalahan internal: ' . $e->getMessage()], 500);
        }
    }

    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        foreach ($vecA as $i => $valA) {
            $valB = $vecB[$i] ?? 0;
            $dotProduct += $valA * $valB;
            $normA += pow($valA, 2);
            $normB += pow($valB, 2);
        }

        if ($normA == 0 || $normB == 0) return 0;
        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }

    /**
     * Clear chat history from session
     */
    public function clearHistory(Request $request)
    {
        session()->forget('chatbot_history');
        return response()->json(['success' => true, 'message' => 'Riwayat chat berhasil dihapus']);
    }
    
    /**
     * Load chat history from database
     */
    public function loadHistory(Request $request)
    {
        $sessionId = $request->cookie('chat_session_id');
        
        if (!$sessionId) {
            return response()->json(['messages' => []]);
        }
        
        $messages = \App\Models\ChatMessage::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->limit(50)
            ->get()
            ->map(function($msg) {
                return [
                    'role' => $msg->role,
                    'text' => $msg->message,
                    'timestamp' => $msg->created_at->format('H:i')
                ];
            });
        
        return response()->json(['messages' => $messages]);
    }
    
    /**
     * Start a new chat session
     */
    public function newSession(Request $request)
    {
        $newSessionId = \Illuminate\Support\Str::uuid()->toString();
        
        // Create new session in database
        \App\Models\ChatSession::create([
            'session_id' => $newSessionId,
            'user_ip' => $request->ip()
        ]);
        
        // Clear in-memory history
        session()->forget('chatbot_history');
        
        return response()->json([
            'success' => true,
            'message' => 'Sesi baru dimulai'
        ])->cookie('chat_session_id', $newSessionId, 43200); // 30 days
    }

    /**
     * Store feedback for analytics (optional)
     */
    public function feedback(Request $request)
    {
        $request->validate([
            'message_id' => 'required|string',
            'type' => 'required|in:like,dislike'
        ]);

        // Optional: Store feedback in database or log file for analytics
        // For now, just return success
        \Log::info('Chatbot Feedback', [
            'message_id' => $request->message_id,
            'type' => $request->type,
            'timestamp' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Terima kasih atas feedback Anda!']);
    }

    /**
     * Export chat history to PDF or TXT
     */
    public function exportChat(Request $request)
    {
        $request->validate([
            'messages' => 'required|array',
            'format' => 'required|in:pdf,txt'
        ]);

        $messages = $request->input('messages');
        $format = $request->input('format');

        if ($format === 'pdf') {
            return $this->exportToPdf($messages);
        }

        return $this->exportToTxt($messages);
    }

    /**
     * Export chat to PDF format
     */
    private function exportToPdf($messages)
    {
        $pdf = Pdf::loadView('exports.chat-pdf', [
            'messages' => $messages,
            'date' => now()->format('d M Y H:i')
        ]);

        return $pdf->download('chat-rpjmd-' . now()->format('YmdHis') . '.pdf');
    }

    /**
     * Export chat to TXT format
     */
    private function exportToTxt($messages)
    {
        $content = "Portal RPJMD Kabupaten Pasuruan - Chat Export\n";
        $content .= "Tanggal: " . now()->format('d M Y H:i') . "\n\n";
        $content .= str_repeat("=", 50) . "\n\n";

        foreach ($messages as $msg) {
            $role = $msg['role'] === 'user' ? 'Anda' : 'AI';
            $text = strip_tags($msg['text']); // Remove HTML tags
            $content .= "{$role}: {$text}\n\n";
        }

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="chat-rpjmd-' . now()->format('YmdHis') . '.txt"');
    }
    
    /**
     * Get system prompt based on language
     */
    private function getSystemPrompt($language, $greeting)
    {
        if ($language === 'en') {
            return "You are the RPJMD Virtual Assistant for Pasuruan Regency, intelligent and helpful.

IDENTITY:
- Name: RPJMD AI Assistant
- Task: Help citizens understand RPJMD documents, development programs, and Bapperida services

GREETING:
- Use greeting: '{$greeting}' (according to current time)
- Example: '{$greeting}! I am RPJMD AI Assistant, ready to help you...'

IMPORTANT RULES:
1. ALWAYS answer in English that is friendly and easy to understand
2. IF there is document context, MUST use information from that document
3. IF not in the document, say honestly: 'This information is not available in the current RPJMD documents'
4. DO NOT fabricate facts or data that don't exist
5. Use Markdown format for neat answers:
   - **Bold** for important points
   - Bullet points (•) for lists
   - Numbering (1. 2. 3.) for steps
   - Short paragraphs (max 3-4 sentences)
   - For TABLE DATA or NUMBERS: Use easy-to-read summary format, DO NOT copy-paste raw tables

ANSWER FORMAT:
- Greeting: {$greeting} (according to time)
- Content: Straight to the point, not verbose
- Closing: Offer further assistance if needed

SPECIFICALLY FOR TABLE/NUMBER DATA:
- DO NOT display raw tables with pipes (|) or lines
- Summarize data in bullet points or paragraph format
- GOOD example: 'Total Regional Expenditure for 2025 is IDR 4.3 Trillion'
- BAD example: '| 5 | REGIONAL EXPENDITURE (Total) | 4,346,062,666,981.00 |'

TOPICS THAT CAN BE ANSWERED:
✅ Vision & Mission of Pasuruan Regency
✅ RPJMD Priority Programs
✅ Development Achievements
✅ Bapperida Services
✅ Planning Documents
✅ Latest News & Information

IF ASKED OUTSIDE THE TOPIC:
'Sorry, I specialize in helping with information about RPJMD and development of Pasuruan Regency. For other questions, please contact the relevant services! 😊'";
        }
        
        // Indonesian (default)
        return "Anda adalah Asisten Virtual RPJMD Kabupaten Pasuruan yang cerdas dan membantu.

IDENTITAS:
- Nama: RPJMD AI Assistant
- Tugas: Membantu warga memahami dokumen RPJMD, program pembangunan, dan layanan Bapperida

SAPAAN:
- Gunakan sapaan: '{$greeting}' (sesuai waktu saat ini)
- Contoh: '{$greeting}! Saya RPJMD AI Assistant, siap membantu Anda...'

ATURAN PENTING:
1. SELALU jawab dalam Bahasa Indonesia yang ramah dan mudah dipahami
2. JIKA ada konteks dokumen, WAJIB gunakan informasi dari dokumen tersebut
3. JIKA tidak ada di dokumen, katakan dengan jujur: 'Informasi ini tidak tersedia dalam dokumen RPJMD saat ini'
4. JANGAN mengarang fakta atau data yang tidak ada
5. Gunakan format Markdown untuk jawaban yang rapi:
   - **Bold** untuk poin penting
   - Bullet points (•) untuk daftar
   - Numbering (1. 2. 3.) untuk langkah-langkah
   - Paragraf pendek (max 3-4 kalimat)
   - Untuk DATA TABEL atau ANGKA: Gunakan format ringkasan yang mudah dibaca, JANGAN copy-paste tabel mentah

FORMAT JAWABAN:
- Sapaan: {$greeting} (sesuai waktu)
- Isi: Langsung to the point, tidak bertele-tele
- Penutup: Tawarkan bantuan lanjutan jika perlu

KHUSUS UNTUK DATA TABEL/ANGKA:
- JANGAN tampilkan tabel mentah dengan pipe (|) atau garis
- Ringkas data dalam format bullet points atau paragraf
- Contoh BAIK: 'Belanja Daerah Total tahun 2025 adalah Rp 4,3 Triliun'
- Contoh BURUK: '| 5 | BELANJA DAERAH (Total) | 4.346.062.666.981,00 |'

TOPIK YANG BISA DIJAWAB:
✅ Visi & Misi Kabupaten Pasuruan
✅ Program Prioritas RPJMD
✅ Capaian Pembangunan
✅ Layanan Bapperida
✅ Dokumen Perencanaan
✅ Berita & Informasi Terkini

JIKA DITANYA DI LUAR TOPIK:
'Maaf, saya khusus membantu informasi seputar RPJMD dan pembangunan Kabupaten Pasuruan. Untuk pertanyaan lain, silakan hubungi layanan terkait ya! 😊'";
    }
}
