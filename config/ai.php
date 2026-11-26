<?php

return [
    // Generation only. Changing this catalog never changes document embeddings.
    'default_provider' => 'gemini',
    'default_model' => 'gemini-2.5-flash',
    'providers' => [
        'gemini' => [
            'label' => 'Google Gemini',
            'enabled' => true,
            // Fallback / verified catalog. Dipakai saat API gagal, timeout, atau key belum siap.
            'models' => [
                'gemini-2.5-flash' => 'Gemini 2.5 Flash',
                'gemini-2.5-pro' => 'Gemini 2.5 Pro',
                'gemini-2.0-flash' => 'Gemini 2.0 Flash',
                'gemini-2.0-flash-lite' => 'Gemini 2.0 Flash-Lite',
                'gemini-1.5-flash' => 'Gemini 1.5 Flash',
                'gemini-1.5-pro' => 'Gemini 1.5 Pro',
            ],
            'fallback_models' => [
                'gemini-2.5-flash' => 'Gemini 2.5 Flash',
                'gemini-2.5-pro' => 'Gemini 2.5 Pro',
                'gemini-2.0-flash' => 'Gemini 2.0 Flash',
                'gemini-2.0-flash-lite' => 'Gemini 2.0 Flash-Lite',
                'gemini-1.5-flash' => 'Gemini 1.5 Flash',
                'gemini-1.5-pro' => 'Gemini 1.5 Pro',
            ],
        ],
        'openai' => [
            'label' => 'OpenAI (GPT)',
            'enabled' => true,
            // Catalog candidates; live account access and RPJMD UAT remain required.
            'models' => ['gpt-4.1-mini-2025-04-14' => 'GPT-4.1 mini'],
            'output_limits' => ['gpt-4.1-mini-2025-04-14' => 2048],
        ],
        'anthropic' => [
            'label' => 'Anthropic (Claude)',
            'enabled' => true,
            'models' => ['claude-sonnet-4-6' => 'Claude Sonnet 4.6'],
            'output_limits' => ['claude-sonnet-4-6' => 2048],
            'version' => '2023-06-01',
        ],
    ],
];
