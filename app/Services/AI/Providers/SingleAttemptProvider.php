<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AiSettings;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\Exceptions\AIProviderException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

abstract class SingleAttemptProvider implements AIProviderInterface
{
    public function __construct(protected AiSettings $settings) {}

    abstract protected function provider(): string;

    abstract protected function endpoint(): string;

    abstract protected function headers(): array;

    abstract protected function payload(string $model, array $messages, int $limit): array;

    abstract protected function parse(array $body): string;

    public function chat(string $model, array $messages): string
    {
        return $this->generate($model, $messages);
    }

    public function test(string $model, array $messages): string
    {
        return $this->generate($model, $messages);
    }

    protected function invalidResponse(): never
    {
        throw new AIProviderException('invalid_response', provider: $this->provider());
    }

    private function generate(string $model, array $messages): string
    {
        $provider = $this->provider();
        // Model IDs can contain dots: read the model map rather than dot notation.
        $limit = config("ai.providers.{$provider}.output_limits", [])[$model] ?? null;
        if (! $this->settings->allows($provider, $model) || ! $this->settings->configured($provider)
            || ! is_int($limit) || $limit < 1) {
            throw new AIProviderException('configuration', provider: $provider);
        }
        try {
            $response = Http::timeout(45)->withOptions(['allow_redirects' => false])
                ->withHeaders($this->headers())->post($this->endpoint(), $this->payload($model, $messages, $limit));
        } catch (ConnectionException) {
            throw new AIProviderException('connection', provider: $provider);
        } catch (\Throwable) {
            throw new AIProviderException('transport', provider: $provider);
        }
        if (! $response->successful()) {
            throw new AIProviderException('http', $response->status(), $provider);
        }
        $body = $response->json();
        if (! is_array($body)) {
            $this->invalidResponse();
        }
        $reply = $this->parse($body);
        if (trim($reply) === '') {
            $this->invalidResponse();
        }

        return $reply;
    }
}
