<?php
$dashboard = 'resources/views/admin/dashboard.blade.php';
$sections = 'new_sections.blade.php';

$dashboardContent = file_get_contents($dashboard);
$sectionsContent = file_get_contents($sections);

$marker = '      <!-- ============================== -->
      <!-- SECTION: MANAJEMEN CAPAIAN     -->';

if (strpos($dashboardContent, $marker) !== false) {
    $parts = explode($marker, $dashboardContent);
    $newContent = $parts[0] . $sectionsContent . "\n" . $marker . $parts[1];
    file_put_contents($dashboard, $newContent);
    echo "Injection successful.";
} else {
    echo "Marker not found.";
}
