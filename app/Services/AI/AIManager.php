<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\Exceptions\AIProviderException;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\OpenAIProvider;
use App\Services\AI\Providers\AnthropicProvider;
use Illuminate\Support\Facades\Log;

class AIManager
{
    public function __construct(private AiSettings $settings, private GeminiProvider $gemini, private OpenAIProvider $openai, private AnthropicProvider $anthropic) {}

    private function provider(string $provider): AIProviderInterface
    {
        return match ($provider) {
            'gemini' => $this->gemini,
            'openai' => $this->openai,
            'anthropic' => $this->anthropic,
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

        return $this->generate($active['provider'], $active['model'], $messages, false);
    }

    public function test(string $provider, string $model): string
    {
        if (! $this->settings->allows($provider, $model)) {
            throw new AIProviderException('configuration');
        }

        return $this->generate($provider, $model, [
            ['role' => 'user', 'text' => 'Jawab singkat dalam Bahasa Indonesia bahwa koneksi model berhasil.'],
        ], true);
    }

    private function generate(string $provider, string $model, array $messages, bool $preview): string
    {
        $start = microtime(true);
        try {
            return $preview ? $this->provider($provider)->test($model, $messages) : $this->provider($provider)->chat($model, $messages);
        } catch (AIProviderException $e) {
            Log::warning('AI generation failed', [
                'provider' => $provider, 'model' => $model, 'category' => $e->errorCode(),
                'status' => $e->status, 'request_id' => $e->requestId,
                'duration_ms' => (int) ((microtime(true) - $start) * 1000),
            ]);
            throw $e;
        }
    }
}
