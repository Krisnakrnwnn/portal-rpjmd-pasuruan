<?php
$transcript_path = 'C:\\Users\\Krisnakrnwnn\\.gemini\\antigravity-ide\\brain\\00c1ce35-0206-4d90-a910-f1e26231ab43\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcript_path, "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        if (strpos($line, 'Showing lines 460 to 620') !== false) {
            file_put_contents('chunk1.txt', $line);
        }
        if (strpos($line, 'Showing lines 620 to 650') !== false) {
            file_put_contents('chunk2.txt', $line);
        }
        if (strpos($line, 'Showing lines 640 to 730') !== false) {
            file_put_contents('chunk3.txt', $line);
        }
    }
    fclose($handle);
}
