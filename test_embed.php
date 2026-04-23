<?php
$apiKey = 'AIzaSyDn9nOf7UJa56ACtba3K1nSFXC8J4iUG_M';
$data = json_encode(['model' => 'models/gemini-embedding-001', 'content' => ['parts' => [['text' => 'apa itu rpjmd?']]]]);
$ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/gemini-embedding-001:embedContent?key=' . $apiKey);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($httpCode === 200) {
    echo "API Key is WORKING! Response code: 200.";
} else {
    echo "FAILED! HTTP Code: " . $httpCode . "\nResponse: " . $response;
}
