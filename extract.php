<?php
$path = 'C:\\Users\\Krisnakrnwnn\\.gemini\\antigravity-ide\\brain\\00c1ce35-0206-4d90-a910-f1e26231ab43\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($path, 'r');
if (!$handle) die("Could not open file.");

$extracted = [];

while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (!$data) continue;
    
    // Sometimes it's in $data['output'] or $data['content']
    $text = "";
    if (isset($data['content'])) {
        $text = $data['content'];
    } elseif (isset($data['output'])) {
        $text = $data['output'];
    } elseif (isset($data['response']['output'])) {
        $text = $data['response']['output'];
    }
    
    if (strpos($text, 'Showing lines 460 to 620') !== false && strpos($text, 'Total Bytes: 142') !== false) {
        $extracted['460-620'] = $text;
    }
    if (strpos($text, 'Showing lines 620 to 650') !== false && strpos($text, 'Total Bytes: 142') !== false) {
        $extracted['620-650'] = $text;
    }
    if (strpos($text, 'Showing lines 640 to 730') !== false && strpos($text, 'Total Bytes: 142') !== false) {
        $extracted['640-730'] = $text;
    }
}
fclose($handle);

function cleanOutput($text) {
    $lines = explode("\n", $text);
    $result = [];
    foreach ($lines as $l) {
        if (preg_match('/^(\d+):\s(.*)$/', $l, $m)) {
            $result[$m[1]] = $m[2];
        }
    }
    return $result;
}

$allLines = [];
foreach ($extracted as $key => $text) {
    $cleaned = cleanOutput($text);
    foreach ($cleaned as $num => $str) {
        $allLines[$num] = $str;
    }
}

ksort($allLines);

$final = implode("\n", $allLines);
file_put_contents('recovered_docs.html', $final);
echo "Recovered " . count($allLines) . " unique lines.\n";
