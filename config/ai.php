<?php

return [
    // Generation only. Changing this catalog never changes document embeddings.
    'default_provider' => 'gemini',
    'default_model' => 'gemini-2.5-flash',
    'providers' => [
        'gemini' => [
            'label' => 'Google Gemini',
            // Verified against Google model/deprecation documentation on 2026-09-15.
            'models' => [
                'gemini-2.5-flash' => 'Gemini 2.5 Flash',
                'gemini-2.5-pro' => 'Gemini 2.5 Pro',
            ],
        ],
    ],
];
