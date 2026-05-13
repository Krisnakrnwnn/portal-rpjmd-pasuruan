<?php

namespace App\Services;

use App\Models\DocumentChunk;
use App\Models\DocumentIngestion;
use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentIngestor
{
    protected $parser;
    protected $apiKey;

    public function __construct()
    {
        $this->parser = new Parser();
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function ingest(DocumentIngestion $ingestion)
    {
        // Gunakan Storage facade agar sinkron dengan Controller
        $filePath = Storage::disk('local')->path('documents/' . $ingestion->file_name);
        
        if (!file_exists($filePath)) {
            $ingestion->update([
                'status' => 'failed',
                'error_message' => 'File tidak ditemukan: ' . $ingestion->file_name
            ]);
            return;
        }

        $ingestion->update([
            'status' => 'processing',
            'started_at' => now()
        ]);

        try {
            // Meningkatkan limit memori menjadi 1GB untuk file sangat besar
            ini_set('memory_limit', '1024M');
            set_time_limit(0);

            // Parse PDF menggunakan Smalot/PdfParser (Tanpa Imagick)
            $pdf = $this->parser->parseFile($filePath);
            $pages = $pdf->getPages();
            $numPages = count($pages);

            $ingestion->update(['total_pages' => $numPages]);

            foreach ($pages as $pageIndex => $page) {
                $pageNumber = $pageIndex + 1;

                try {
                    // Ekstraksi teks langsung dari halaman
                    $text = $page->getText();
                    
                    // Bersihkan teks dari karakter non-printable
                    $text = preg_replace('/[^\x09\x0A\x0D\x20-\x7E\xA0-\xFF]/u', '', $text); 
                    $text = preg_replace('/\s+/', ' ', trim($text)); 

                    if (!empty($text) && strlen($text) >= 10) {
                        // Pecah teks menjadi chunk (sekitar 1500 karakter)
                        $chunks = str_split($text, 1500);

                        foreach ($chunks as $chunkIndex => $chunk) {
                            $response = Http::timeout(60)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-embedding-001:embedContent?key={$this->apiKey}", [
                                'model' => 'models/gemini-embedding-001',
                                'content' => [
                                    'parts' => [
                                        ['text' => "Document: {$ingestion->original_name}\nPage: $pageNumber\nContent: $chunk"]
                                    ]
                                ]
                            ]);

                            if ($response->successful()) {
                                $embedding = $response->json('embedding.values');

                                DocumentChunk::create([
                                    'document_name' => $ingestion->original_name,
                                    'page_number' => $pageNumber,
                                    'chunk_text' => $chunk,
                                    'embedding' => $embedding
                                ]);
                                
                                // Jeda sedikit untuk menghormati rate limit API Gemini
                                usleep(500000); // 0.5 detik
                            } else {
                                Log::error("Gemini API Error for {$ingestion->file_name} page $pageNumber: " . $response->body());
                            }
                        }
                    }

                    // Update progres halaman di database
                    $ingestion->increment('processed_pages');

                } catch (\Exception $e) {
                    Log::error("Error ingesting page $pageNumber of {$ingestion->file_name}: " . $e->getMessage());
                    // Lanjut ke halaman berikutnya jika satu halaman gagal
                }
            }

            $ingestion->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error("Error ingesting {$ingestion->file_name}: " . $e->getMessage());
            $ingestion->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
        }
    }
}
