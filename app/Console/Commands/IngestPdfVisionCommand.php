<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DocumentChunk;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('rag:ingest-vision {filename?} {--all} {--resume}')]
#[Description('Ingest PDFs using Gemini Vision to accurately capture tables and formulas.')]
class IngestPdfVisionCommand extends Command
{
    public function handle()
    {
        $directory = env('RAG_PDF_PATH', storage_path('app/documents'));
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            $this->error('GEMINI_API_KEY is not set in .env');
            return;
        }

        if (!class_exists('Imagick')) {
            $this->error('PHP Imagick extension is required for Vision ingestion.');
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
            $this->error("Please provide a filename or use --all");
            return;
        }

        ini_set('memory_limit', '-1');

        foreach ($files as $file) {
            $filePath = rtrim($directory, '/') . '/' . $file;
            if (!file_exists($filePath)) {
                $this->error("File not found: $filePath");
                continue;
            }

            $this->info("Processing with VISION: $file ...");

            try {
                // Get page count first without loading everything if possible
                // We'll use a temporary Imagick instance for this
                $tempImagick = new \Imagick();
                $tempImagick->pingImage($filePath);
                $numPages = $tempImagick->getNumberImages();
                $tempImagick->clear();
                $tempImagick->destroy();

                $this->info("Found $numPages pages.");

                for ($i = 0; $i < $numPages; $i++) {
                    $pageNumber = $i + 1;

                    if ($resume) {
                        $exists = DocumentChunk::where('document_name', $file)
                            ->where('page_number', $pageNumber)
                            ->exists();
                        if ($exists) {
                            $this->line("  [SKIP] Page $pageNumber already ingested.");
                            continue;
                        }
                    }

                    $this->line("Reading page $pageNumber with Gemini Vision...");

                    // Create fresh instance for each page to keep memory usage low
                    $imagick = new \Imagick();
                    $imagick->setResolution(150, 150);
                    // ONLY read the specific page: filename.pdf[index]
                    $imagick->readImage($filePath . "[" . $i . "]");
                    $imagick->setImageFormat('png');
                    
                    $imageData = base64_encode($imagick->getImageBlob());

                    // 1. Ask Gemini to describe/transcribe the page
                    $transcription = $this->transcribePageWithVision($imageData, $apiKey);

                    if (!$transcription) {
                        $this->error("Failed to transcribe page $pageNumber. Skipping.");
                        $imagick->clear();
                        $imagick->destroy();
                        continue;
                    }

                    // 2. Get embedding for the transcription
                    $this->line("  Getting embedding for page $pageNumber...");
                    $embedding = $this->getEmbedding($transcription, $apiKey, $file, $pageNumber);

                    if ($embedding) {
                        DocumentChunk::create([
                            'document_name' => $file,
                            'page_number' => $pageNumber,
                            'chunk_text' => $transcription,
                            'embedding' => $embedding
                        ]);
                        $this->info("  [SUCCESS] Page $pageNumber ingested.");
                    }

                    // Clean up after each page
                    $imagick->clear();
                    $imagick->destroy();

                    // Respect rate limits (Free tier is ~15 RPM)
                    sleep(4);
                }

            } catch (\Exception $e) {
                $this->error("Error on $file: " . $e->getMessage());
            }
        }
    }

    private function transcribePageWithVision($base64Image, $apiKey)
    {
        $prompt = "Ini adalah halaman dari dokumen perencanaan daerah (RPJMD/RPJPD). 
        Tugas Anda:
        1. Ekstrak semua teks secara akurat.
        2. Jika ada TABEL, rekonstruksi ulang ke format Markdown table. Pastikan angka dan barisnya tidak tertukar.
        3. Jika ada RUMUS, jelaskan atau tulis dalam format yang mudah dibaca.
        4. Jangan memberikan komentar pembuka/penutup, langsung berikan hasil transkripsinya saja.";

        $response = Http::timeout(60)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => 'image/png',
                                'data' => $base64Image
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        return $response->json('candidates.0.content.parts.0.text');
    }

    private function getEmbedding($text, $apiKey, $file, $page)
    {
        $response = Http::timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-embedding-001:embedContent?key={$apiKey}", [
            'model' => 'models/gemini-embedding-001',
            'content' => [
                'parts' => [
                    ['text' => "Document: $file\nPage: $page\nContent: $text"]
                ]
            ]
        ]);

        return $response->json('embedding.values');
    }
}
