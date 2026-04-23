<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$chunks = App\Models\DocumentChunk::where('chunk_text', 'ilike', '%visi%')
    ->limit(10)->get();

foreach($chunks as $c) {
    echo "\n=== FILE: {$c->document_name} | PAGE: {$c->page_number} ===\n";
    echo substr($c->chunk_text, 0, 800) . "\n";
}
