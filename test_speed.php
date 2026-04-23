<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$start = microtime(true);
$allChunks = \App\Models\DocumentChunk::all();
echo "Loaded " . count($allChunks) . " chunks in " . (microtime(true) - $start) . " seconds\n";

$questionEmbedding = array_fill(0, 768, 0.1); 

$similarities = [];
foreach ($allChunks as $chunk) {
    // cosineSimilarity simulation
    $vecA = $questionEmbedding;
    $vecB = $chunk->embedding;
    $dotProduct = 0;
    $normA = 0;
    $normB = 0;

    foreach ($vecA as $i => $valA) {
        $valB = $vecB[$i] ?? 0;
        $dotProduct += $valA * $valB;
        $normA += pow($valA, 2);
        $normB += pow($valB, 2);
    }
    $similarities[] = $dotProduct;
}

echo "Finished similarity check in " . (microtime(true) - $start) . " seconds\n";
