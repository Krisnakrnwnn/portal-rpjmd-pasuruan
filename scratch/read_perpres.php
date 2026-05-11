<?php
require 'vendor/autoload.php';
$parser = new \Smalot\PdfParser\Parser();
try {
    // Membaca file dengan batasan agar tidak memakan terlalu banyak memori
    $pdf = $parser->parseFile('storage/app/documents/Perpres Nomor 12 Tahun 2025 - Lampiran I Batang Tubuh.pdf');
    $pages = $pdf->getPages();
    echo "Total Halaman: " . count($pages) . "\n\n";
    // Ambil teks dari halaman 1 saja
    echo $pages[0]->getText();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
