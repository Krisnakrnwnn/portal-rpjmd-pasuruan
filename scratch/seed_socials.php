<?php

use App\Models\Profile;

$data = [
    [
        'key' => 'ig_link',
        'title' => 'Link Instagram',
        'content' => 'https://instagram.com/'
    ],
    [
        'key' => 'fb_link',
        'title' => 'Link Facebook',
        'content' => 'https://facebook.com/'
    ],
    [
        'key' => 'wa_number',
        'title' => 'Nomor WhatsApp',
        'content' => '628123456789'
    ]
];

foreach ($data as $d) {
    Profile::updateOrCreate(['key' => $d['key']], $d);
}

echo "Social profiles seeded successfully!";
