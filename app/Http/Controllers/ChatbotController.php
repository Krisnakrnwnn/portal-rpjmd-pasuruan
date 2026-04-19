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
                // STEP 2: Find top 3-5 most similar chunks using Cosine Similarity in PHP
                // (Warning: For 20k+ chunks, pure PHP is slow. Consider Pinecone or DB triggers in production)
                $allChunks = DocumentChunk::all(); // Memory intensive if huge!
                
                $similarities = [];
                foreach ($allChunks as $chunk) {
                    $score = $this->cosineSimilarity($questionEmbedding, $chunk->embedding);
                    $similarities[] = [
                        'score' => $score,
                        'text' => "[File: {$chunk->document_name}, Hal: {$chunk->page_number}]\n" . $chunk->chunk_text
                    ];
                }

                // Sort descending by score
                usort($similarities, fn($a, $b) => $b['score'] <=> $a['score']);
                
                // Take top 4 chunks
                $topChunks = array_slice($similarities, 0, 4);
                $contextPieces = array_column($topChunks, 'text');
                
                // Prevent very low relevance
                if ($topChunks[0]['score'] > 0.4) {
                    $contextText = "Gunakan konteks dokumen RPJMD berikut untuk menjawab:\n\n" . implode("\n\n---\n\n", $contextPieces);
                }
            }

            // STEP 3: Prompt Gemini to answer
            $systemInstruction = "Anda adalah Asisten Virtual Cerdas untuk Rencana Pembangunan Jangka Menengah Daerah (RPJMD) Kota Pasuruan. Jawablah warga Kota Pasuruan dengan ramah, berwibawa, namun mudah dipahami. Jika konteks dokumen disediakan di bawah, JAWAB BERDASARKAN DOKUMEN TERSEBUT. Jika tidak ada konteks atau pertanyaannya di luar konteks, jawab dengan pengetahuan umum atau beri tahu bahwa dokumen belum mencantumkan hal tersebut.";

            $prompt = $systemInstruction . "\n\n" . $contextText . "\n\nPertanyaan Warga: " . $userMessage;

            $chatResponse = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($chatResponse->successful()) {
                $reply = $chatResponse->json('candidates.0.content.parts.0.text') ?? 'Saya tidak dapat memberikan jawaban saat ini.';
                
                // Format markdown if needed (Gemini returns markdown, can be processed client-side)
                return response()->json(['reply' => $reply]);
            }

            return response()->json(['reply' => 'Error dari API Gemini: ' . $chatResponse->body()], 500);

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
