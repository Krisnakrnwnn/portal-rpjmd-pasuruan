<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\DocumentChunk;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Smalot\PdfParser\Parser;

class IngestPdfCommand extends Command
{
    protected $signature = 'rag:ingest {filename?} {--all} {--resume}';
    protected $description = 'Extract text from PDFs, chunk them, get embeddings, and save to DB.';

    public function handle()
    {
        $directory = env('RAG_PDF_PATH', storage_path('app/documents'));
        
        if (!is_dir($directory)) {
            $this->error("Directory not found: $directory");
            return;
        }

        $filename = $this->argument('filename');
        $all = $this->option('all');

        $resume = $this->option('resume');

        if ($all) {
            $files = collect(File::files($directory))->map->getFilename()->toArray();
        } elseif ($filename) {
            $files = [$filename];
        } else {
            // Kita ubah default ke file berukuran 500KB agar berhasil tanpa error memory
            $files = ['Perpres Nomor 12 Tahun 2025 RPJMN 2025-2029.pdf'];
            $this->info("No filename provided. Defaulting to smaller test file 'Perpres Nomor 12 Tahun 2025 RPJMN 2025-2029.pdf'.");
        }

        if ($resume) {
            $this->info('Mode RESUME aktif: halaman yang sudah di-ingest akan dilewati.');
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
                $this->error("File not found: $filePath");
                continue;
            }

            $this->info("Processing: $file ...");

            try {
                // Use Imagick to get page count and process one by one
                $tempImagick = new \Imagick();
                $tempImagick->pingImage($filePath);
                $numPages = $tempImagick->getNumberImages();
                $tempImagick->clear();
                $tempImagick->destroy();

                $this->info("Found $numPages pages.");

                for ($pageIndex = 0; $pageIndex < $numPages; $pageIndex++) {
                    $pageNumber = $pageIndex + 1;

                    if ($resume) {
                        $alreadyIngested = DocumentChunk::where('document_name', $file)
                            ->where('page_number', $pageNumber)
                            ->exists();
                        if ($alreadyIngested) {
                            $this->line("  [SKIP] Page {$pageNumber} already ingested.");
                            continue;
                        }
                    }

                    $this->line("Extracting text from page {$pageNumber}...");

                    try {
                        // Extract single page using Imagick
                        $singlePageImagick = new \Imagick();
                        // Set resolution before reading to keep it light
                        $singlePageImagick->setResolution(72, 72); 
                        $singlePageImagick->readImage($filePath . "[" . $pageIndex . "]");
                        $tempPagePath = storage_path("app/temp_page_{$pageNumber}.pdf");
                        $singlePageImagick->writeImage($tempPagePath);
                        $singlePageImagick->clear();
                        $singlePageImagick->destroy();

                        // Parse the single-page PDF
                        $pagePdf = $parser->parseFile($tempPagePath);
                        $text = $pagePdf->getText();
                        
                        // Cleanup temp file immediately
                        if (file_exists($tempPagePath)) unlink($tempPagePath);

                        // Cleaning text
                        $text = preg_replace('/[^\x09\x0A\x0D\x20-\x7E\xA0-\xFF]/u', '', $text); 
                        $text = preg_replace('/\s+/', ' ', trim($text)); 

                        if (empty($text) || strlen($text) < 10) {
                            $this->line("  [SKIP] Page {$pageNumber} has no readable text.");
                            continue;
                        }

                        // Chunking
                        $chunks = str_split($text, 1500);

                        foreach ($chunks as $chunkIndex => $chunk) {
                            $this->line("  Embedding page {$pageNumber} (chunk {$chunkIndex})...");
                            
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
                                
                                // Rate limit respect
                                sleep(1); 
                            } else {
                                $this->error("  Failed to embed page $pageNumber: " . $response->body());
                            }
                            
                            unset($embedding);
                            unset($response);
                        }

                        // AGGRESSIVE MEMORY CLEANUP
                        unset($text);
                        unset($pagePdf);
                        unset($chunks);
                        gc_collect_cycles();
                        
                    } catch (\Exception $pageException) {
                        $this->error("  Error on page $pageNumber: " . $pageException->getMessage());
                    }
                }
                
                $this->info("Completed: $file!");

            } catch (\Exception $e) {
                $currentPage = isset($pageNumber) ? " (Halaman $pageNumber)" : "";
                $this->error("Error pada file $file$currentPage: " . $e->getMessage());
            }
        }
    }
}
