<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AiSettings;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\Exceptions\AIProviderException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;

class GeminiProvider implements AIProviderInterface
{
    public function __construct(private AiSettings $settings) {}

    public function chat(string $model, array $messages): string
    {
        return $this->generate($model, $messages, 3, false);
    }

    public function test(string $model, array $messages): string
    {
        return $this->generate($model, $messages, 1, true);
    }

    private function generate(string $model, array $messages, int $attempts, bool $strict): string
    {
        if (! $this->settings->allows('gemini', $model) || ! config('services.gemini.api_key')) {
            throw new AIProviderException('configuration');
        }
        $contents = array_map(static fn (array $message) => [
            'role' => $message['role'] === 'assistant' ? 'model' : 'user',
            'parts' => [['text' => $message['text']]],
        ], $messages);

        for ($attempt = 0; $attempt < $attempts; $attempt++) {
            try {
                $response = Http::timeout(45)
                    ->withHeaders(['x-goog-api-key' => config('services.gemini.api_key')])
                    ->withOptions(['allow_redirects' => false])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", ['contents' => $contents]);
            } catch (ConnectionException) {
                throw new AIProviderException('connection');
            } catch (\Throwable) {
                throw new AIProviderException('transport');
            }
            if ($response->successful()) {
                $reply = $response->json('candidates.0.content.parts.0.text');
                $blocked = $response->json('promptFeedback.blockReason')
                    || in_array($response->json('candidates.0.finishReason'), ['SAFETY', 'BLOCKLIST', 'PROHIBITED_CONTENT', 'SPII'], true);
                if ($blocked || ! is_string($reply) || trim($reply) === '') {
                    if ($strict) {
                        throw new AIProviderException('invalid_response');
                    }

                    return 'Saya tidak dapat memberikan jawaban saat ini.';
                }

                return $reply;
            }
            if (! in_array($response->status(), [429, 503], true)) {
                break;
            }
            // Retain the production delay even after the final failed attempt.
            if (! $strict) {
                Sleep::for(3)->seconds();
            }
        }

        throw new AIProviderException('http', $response->status());
    }
}
