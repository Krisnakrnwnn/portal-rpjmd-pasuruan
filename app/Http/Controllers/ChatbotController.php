<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\DocumentChunk;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string']);
        $userMessage = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json(['reply' => 'Sistem tidak terkonfigurasi (API Key hilang).'], 500);
        }

        try {
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

            // STEP 3: Prompt Gemini to answer (Instruksi dibuat jauh lebih pintar dan ketat)
            $systemInstruction = "Anda adalah Asisten Virtual Cerdas Rencana Pembangunan Jangka Menengah Daerah (RPJMD) Kabupaten Pasuruan.
Instruksi Penting:
1. Jawablah pengguna dengan bahasa yang ramah, hangat, berwibawa, dan mudah dipahami.
2. JIKA dokumen konteks disediakan di bawah, JAWAB WAJIB BERDASARKAN DOKUMEN TERSEBUT.
3. JIKA dokumen tidak menebutkan jawabannya, JANGAN MENGARANG FAKTA. Katakan sejujurnya bahwa informasi yang diminta tidak tercantum dalam dokumen RPJMD saat ini.
4. JIKA menyebutkan daftar, visi misi, atau langkah-langkah, WAJIB gunakan format Markdown lengkap (seperti Bullet points, angka, teks tebal/bold, atau garis baru) agar teks terlihat sangat rapi dan mudah dibaca oleh warga.
5. Jika pengguna sekadar menyapa ('Halo', 'Hai'), balas sapaan tersebut dan tawarkan bantuan terkait info dokumen Pemerintah Kabupaten Pasuruan.";

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
                return response()->json(['reply' => $reply]);
            }

            // Jika masih error setelah di-retry (Atau gagal gara-gara alasan lain)
            $errorData = $chatResponse ? $chatResponse->json() : null;
            $status = $chatResponse ? $chatResponse->status() : 500;
            
            if ($status == 503) {
                return response()->json(['reply' => '🙏 Mohon maaf, server AI kami saat ini sedang sangat antre dipanggil oleh orang lain dari seluruh dunia (Server Overload). Silakan coba kirim ulang pertanyaan Anda dalam beberapa detik/menit ke depan.']);
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
}
