<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\DocumentChunk;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Smalot\PdfParser\Parser;

#[Signature('rag:ingest {filename?} {--all}')]
#[Description('Extract text from PDFs, chunk them, get embeddings, and save to DB')]
class IngestPdfCommand extends Command
{
    public function handle()
    {
        $directory = 'd:/PBL/AssetsPDF';
        
        if (!is_dir($directory)) {
            $this->error("Directory not found: $directory");
            return;
        }

        $filename = $this->argument('filename');
        $all = $this->option('all');

        if ($all) {
            $files = collect(File::files($directory))->map->getFilename()->toArray();
        } elseif ($filename) {
            $files = [$filename];
        } else {
            // Kita ubah default ke file berukuran 500KB agar berhasil tanpa error memory
            $files = ['Perpres Nomor 12 Tahun 2025 RPJMN 2025-2029.pdf'];
            $this->info("No filename provided. Defaulting to smaller test file 'Perpres Nomor 12 Tahun 2025 RPJMN 2025-2029.pdf'.");
        }

        // Buka batasan Memory Limit bawaan PHP yang sebelumnya 512MB
        ini_set('memory_limit', '-1');

        $parser = new Parser();
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            $this->error('GEMINI_API_KEY is not set in .env');
            return;
        }

        foreach ($files as $file) {
            $filePath = rtrim($directory, '/') . '/' . $file;

            if (!file_exists($filePath)) {
                // Try literal fallback
                $filePath = 'd:\PBL\AssetsPDF\\' . $file;
                if (!file_exists($filePath)) {
                    $this->error("File not found: $filePath");
                    continue;
                }
            }

            $this->info("Processing: $file ...");

            try {
                $pdf = $parser->parseFile($filePath);
                $pages = $pdf->getPages();

                $this->info("Found " . count($pages) . " pages.");

                foreach ($pages as $pageIndex => $page) {
                    $pageNumber = $pageIndex + 1;
                    $text = $page->getText();
                    
                    // Bersihkan teks dari karakter non-UTF8 yang rusak (sering terjadi di PDF hasil scan)
                    $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
                    $text = preg_replace('/[\x00-\x1F\x7F]/u', '', $text); // Hapus karakter kontrol
                    $text = preg_replace('/\s+/', ' ', trim($text)); // Clean multiple spaces/newlines

                    if (empty($text) || strlen($text) < 50) continue; // Skip empty or tiny pages

                    // Simple fixed-length chunking (every ~1000 characters)
                    $chunks = str_split($text, 1000);

                    foreach ($chunks as $chunkIndex => $chunk) {
                        $this->line("Embedding page {$pageNumber} (chunk {$chunkIndex})...");
                        
                        $retryCount = 0;
                        $maxRetries = 8;
                        $success = false;

                        while (!$success && $retryCount < $maxRetries) {
                            // Call Gemini API for embedding
                            $response = Http::timeout(60)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-embedding-001:embedContent?key={$apiKey}", [
                                'model' => 'models/gemini-embedding-001',
                                'content' => [
                                    'parts' => [
                                        ['text' => "Document: $file\nPage: $pageNumber\nContent: $chunk"]
                                    ]
                                ]
                            ]);

                            if ($response->successful()) {
                                $embedding = $response->json('embedding.values');

                                DocumentChunk::create([
                                    'document_name' => $file,
                                    'page_number' => $pageNumber,
                                    'chunk_text' => $chunk,
                                    'embedding' => $embedding
                                ]);
                                $success = true;
                                
                                // Explicitly delay 3 seconds between successful hits to respect the 15 RPM free tier limit
                                sleep(3);
                            } else {
                                if ($response->status() === 429) {
                                    $sleepTime = 20 + ($retryCount * 15); // 20s, 35s, 50s, 65s...
                                    $this->warn("Rate limit hit (429). Sleeping for {$sleepTime} seconds before retrying...");
                                    sleep($sleepTime);
                                    $retryCount++;
                                } else {
                                    // Other fatal API error
                                    $this->error("Failed to embed chunk on page $pageNumber: " . $response->body());
                                    break; // Skip chunk if it's a fatal error (not rate limit)
                                }
                            }
                        }
                        
                        if (!$success) {
                            $this->error("Failed to inject chunk after $maxRetries retries. Skipping chunk.");
                        }
                        
                        // Prevent overloading API limits
                        usleep(300000); // 0.3s delay between chunks
                    }
                }
                
                $this->info("Completed: $file!");

            } catch (\Exception $e) {
                $this->error("Error extracting text from $file: " . $e->getMessage());
            }
        }
    }
}
