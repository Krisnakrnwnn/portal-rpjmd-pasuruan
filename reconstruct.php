<?php
function extractLines($filename) {
    if (!file_exists($filename)) return [];
    $content = file_get_contents($filename);
    $data = json_decode($content, true);
    if (!$data) return [];
    
    // Check if output is inside response or directly in the object
    // Depending on transcript format, it might be nested
    $output = "";
    if (isset($data['output'])) {
        $output = $data['output'];
    } elseif (isset($data['response']['output'])) {
        $output = $data['response']['output'];
    } elseif (isset($data['content'])) {
        $output = $data['content'];
    }
    
    $lines = explode("\n", $output);
    $result = [];
    foreach ($lines as $l) {
        // Strip line numbers e.g. "460:           @empty"
        if (preg_match('/^\d+:\s(.*)$/', $l, $matches)) {
            $result[] = $matches[1];
        }
    }
    return $result;
}

$c1 = extractLines('chunk1.txt');
$c2 = extractLines('chunk2.txt');
$c3 = extractLines('chunk3.txt');

// We know c1 is 460-620.
// c2 is 620-650.
// c3 is 640-730.

// The file should have section-dokumen inserted back.
// But wait, the file from git might be completely different in structure!
// Let's just create a recovered_block.html containing the joined lines and see.
$all = array_merge($c1, $c2, $c3);
file_put_contents('recovered_block.html', implode("\n", $all));
echo "Recovered " . count($all) . " lines to recovered_block.html";
