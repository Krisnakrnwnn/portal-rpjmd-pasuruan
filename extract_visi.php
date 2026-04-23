<?php
require __DIR__.'/vendor/autoload.php';

$parser = new \Smalot\PdfParser\Parser();
$pdfPath = 'd:/PBL/AssetsPDF/RPJMD 2025-2029 (F4) 05122025 (2).pdf';

$pdf = $parser->parseFile($pdfPath);
$pages = $pdf->getPages();

for ($i = 330; $i <= 337; $i++) {
    echo "=== PAGE " . ($i + 1) . " ===\n";
    echo $pages[$i]->getText() . "\n\n";
}
