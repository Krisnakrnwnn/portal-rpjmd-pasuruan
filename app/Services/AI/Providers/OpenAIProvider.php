<?php

namespace App\Services\AI\Providers;

class OpenAIProvider extends SingleAttemptProvider
{
    protected function provider(): string
    {
        return 'openai';
    }

    protected function endpoint(): string
    {
        return 'https://api.openai.com/v1/responses';
    }

    protected function headers(): array
    {
        return ['Authorization' => 'Bearer '.config('services.openai.api_key')];
    }

    protected function payload(string $model, array $messages, int $limit): array
    {
        return [
            'model' => $model,
            'input' => array_map(fn ($message) => ['role' => $message['role'], 'content' => $message['text']], $messages),
            'store' => false,
            'max_output_tokens' => $limit,
        ];
    }

    protected function parse(array $body): string
    {
        if (($body['status'] ?? null) !== 'completed' || ! empty($body['error']) || ! is_array($body['output'] ?? null)) {
            $this->invalidResponse();
        }
        $texts = [];
        foreach ($body['output'] as $item) {
            if (! is_array($item)) {
                $this->invalidResponse();
            }
            if (($item['type'] ?? null) === 'reasoning') {
                continue; // Internal reasoning is never user-visible.
            }
            if (($item['type'] ?? null) !== 'message' || ($item['role'] ?? null) !== 'assistant'
                || ($item['status'] ?? null) !== 'completed' || ! is_array($item['content'] ?? null)) {
                $this->invalidResponse();
            }
            foreach ($item['content'] as $part) {
                if (! is_array($part) || ($part['type'] ?? null) !== 'output_text' || ! is_string($part['text'] ?? null)) {
                    $this->invalidResponse();
                }
                $texts[] = $part['text'];
            }
        }

        return implode("\n", $texts);
    }
}
