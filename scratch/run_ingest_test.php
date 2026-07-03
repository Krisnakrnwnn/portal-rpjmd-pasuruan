<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $job = App\Models\DocumentIngestion::find(4);
    if (!$job) {
        die("DocumentIngestion ID 4 not found.\n");
    }
    echo "Running ingest for file: " . $job->file_name . "\n";
    $ingestor = new App\Services\DocumentIngestor();
    $ingestor->ingest($job);
    echo "Ingest completed successfully or failed internally.\n";
} catch (\Throwable $e) {
    echo "Caught Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
