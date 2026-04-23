<?php

$directories_to_scan = [
    'resources/views',
    'app',
    'database/seeders',
    'public',
    'routes',
    'config'
];
$base_dir = "d:\\PBL\\pbls6";

$replacements = [
    '/\\bKota Pasuruan\\b/' => 'Kabupaten Pasuruan',
    '/\\bkota pasuruan\\b/' => 'kabupaten pasuruan',
    '/\\bKOTA PASURUAN\\b/' => 'KABUPATEN PASURUAN',
    '/\\bPemkot Pasuruan\\b/' => 'Pemkab Pasuruan',
    '/\\bpemkot pasuruan\\b/' => 'pemkab pasuruan',
    '/\\bPemerintah Kota Pasuruan\\b/' => 'Pemerintah Kabupaten Pasuruan',
    '/\\bpemerintah kota pasuruan\\b/' => 'pemerintah kabupaten pasuruan',
    '/\\bpemerintah kota\\b/' => 'pemerintah kabupaten',
    '/\\bPemerintah Kota\\b/' => 'Pemerintah Kabupaten',
    '/Visi Misi Kota/' => 'Visi Misi Kabupaten',
    '/Misi Kota/' => 'Misi Kabupaten',
];

function replace_in_file($filepath, $replacements) {
    global $base_dir;
    
    $content = file_get_contents($filepath);
    $new_content = $content;
    
    foreach ($replacements as $pattern => $replacement) {
        $new_content = preg_replace($pattern, $replacement, $new_content);
    }
    
    if ($new_content !== $content) {
        file_put_contents($filepath, $new_content);
        echo "Updated: " . str_replace($base_dir, '', $filepath) . "\n";
    }
}

function process_directory($dir, $replacements) {
    if (!is_dir($dir)) return;
    
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $ext = $file->getExtension();
            if (in_array($ext, ['php', 'js', 'json', 'html', 'env', 'txt'])) {
                replace_in_file($file->getPathname(), $replacements);
            }
        }
    }
}

foreach ($directories_to_scan as $dir) {
    $path = $base_dir . DIRECTORY_SEPARATOR . $dir;
    process_directory($path, $replacements);
}

// Also process environment and related root files
replace_in_file($base_dir . DIRECTORY_SEPARATOR . '.env', $replacements);
replace_in_file($base_dir . DIRECTORY_SEPARATOR . '.env.example', $replacements);

echo "Done\n";

?>
