<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$chunks = \App\Models\DocumentChunk::selectRaw('document_name, count(*) as total, max(page_number) as last_page')
    ->groupBy('document_name')
    ->get();

if ($chunks->isEmpty()) {
    echo "Belum ada data yang di-ingest.\n";
} else {
    echo str_pad("Dokumen", 60) . str_pad("Chunks", 10) . "Halaman Terakhir\n";
    echo str_repeat("-", 80) . "\n";
    foreach ($chunks as $c) {
        echo str_pad($c->document_name, 60) . str_pad($c->total, 10) . $c->last_page . "\n";
    }
    echo "\nTotal chunks: " . \App\Models\DocumentChunk::count() . "\n";
}
