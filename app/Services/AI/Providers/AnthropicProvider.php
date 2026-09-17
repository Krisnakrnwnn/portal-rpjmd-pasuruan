<?php

namespace App\Services\AI\Providers;

class AnthropicProvider extends SingleAttemptProvider
{
    protected function provider(): string
    {
        return 'anthropic';
    }

    protected function endpoint(): string
    {
        return 'https://api.anthropic.com/v1/messages';
    }

    protected function headers(): array
    {
        return [
            'x-api-key' => config('services.anthropic.api_key'),
            'anthropic-version' => config('ai.providers.anthropic.version'),
        ];
    }

    protected function payload(string $model, array $messages, int $limit): array
    {
        return [
            'model' => $model,
            'messages' => array_map(fn ($message) => ['role' => $message['role'], 'content' => $message['text']], $messages),
            'max_tokens' => $limit,
        ];
    }

    protected function parse(array $body): string
    {
        if (($body['type'] ?? null) !== 'message' || ($body['role'] ?? null) !== 'assistant'
            || ($body['stop_reason'] ?? null) !== 'end_turn' || ! is_array($body['content'] ?? null)) {
            $this->invalidResponse();
        }
        $texts = [];
        foreach ($body['content'] as $part) {
            if (! is_array($part)) {
                $this->invalidResponse();
            }
            if (in_array($part['type'] ?? null, ['thinking', 'redacted_thinking'], true)) {
                continue;
            }
            if (($part['type'] ?? null) !== 'text' || ! is_string($part['text'] ?? null)) {
                $this->invalidResponse();
            }
            $texts[] = $part['text'];
        }

        return implode("\n", $texts);
    }
}
