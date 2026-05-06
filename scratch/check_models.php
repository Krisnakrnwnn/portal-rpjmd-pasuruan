<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiKey = env('GEMINI_API_KEY');

if (!$apiKey) {
    echo "Error: GEMINI_API_KEY is not set in .env\n";
    exit(1);
}

echo "Checking available models for your API Key...\n";

$url = "https://generativelanguage.googleapis.com/v1beta/models?key=$apiKey";
$response = file_get_contents($url);

if ($response === false) {
    echo "Error: Failed to connect to Gemini API.\n";
    exit(1);
}

$data = json_decode($response, true);

echo "\nSupported Models:\n";
foreach ($data['models'] as $model) {
    echo "- " . $model['name'] . " (Methods: " . implode(', ', $model['supportedGenerationMethods']) . ")\n";
}
