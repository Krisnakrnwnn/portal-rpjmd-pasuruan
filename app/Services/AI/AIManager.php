<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\Exceptions\AIProviderException;
use App\Services\AI\Providers\GeminiProvider;

class AIManager
{
    public function __construct(private AiSettings $settings, private GeminiProvider $gemini) {}

    private function provider(string $provider): AIProviderInterface
    {
        return match ($provider) {
            'gemini' => $this->gemini,
            default => throw new AIProviderException('configuration'),
        };
    }

    public function chat(array $history, string $prompt): string
    {
        // Adapt the existing persisted history without rewriting it in the session.
        $messages = array_map(static fn (array $message) => [
            'role' => $message['role'] === 'model' ? 'assistant' : 'user',
            'text' => $message['parts'][0]['text'],
        ], $history);
        $messages[] = ['role' => 'user', 'text' => $prompt];
        $active = $this->settings->current();

        return $this->provider($active['provider'])->chat($active['model'], $messages);
    }

    public function test(string $provider, string $model): string
    {
        if (! $this->settings->allows($provider, $model)) {
            throw new AIProviderException('configuration');
        }

        return $this->provider($provider)->test($model, [
            ['role' => 'user', 'text' => 'Jawab singkat dalam Bahasa Indonesia bahwa koneksi model berhasil.'],
        ]);
    }
}
