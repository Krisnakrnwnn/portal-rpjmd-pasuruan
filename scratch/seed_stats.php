<?php

use App\Models\Stat;

$stats = [
    ['key' => 'total_progress', 'value' => '78.4', 'label' => 'Total Progress RPJMD'],
    ['key' => 'program_berjalan', 'value' => '54', 'label' => 'Program Berjalan'],
    ['key' => 'target_terlampaui', 'value' => '32', 'label' => 'Terlampaui Target'],
];

foreach ($stats as $s) {
    Stat::updateOrCreate(['key' => $s['key']], $s);
}

echo "Stats seeded successfully!";
