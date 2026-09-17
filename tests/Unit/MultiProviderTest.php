<?php

namespace Tests\Unit;

use App\Services\AI\Exceptions\AIProviderException;
use App\Services\AI\Providers\AnthropicProvider;
use App\Services\AI\Providers\OpenAIProvider;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MultiProviderTest extends TestCase
{
    public static function providers(): array
    {
        return [
            ['openai', OpenAIProvider::class, 'gpt-4.1-mini-2025-04-14', 'https://api.openai.com/v1/responses'],
            ['anthropic', AnthropicProvider::class, 'claude-sonnet-4-6', 'https://api.anthropic.com/v1/messages'],
        ];
    }

    public static function body(string $provider): array
    {
        return $provider === 'openai'
            ? ['status' => 'completed', 'output' => [
                ['type' => 'reasoning', 'summary' => [['text' => 'private reasoning']]],
                ['type' => 'message', 'role' => 'assistant', 'status' => 'completed', 'content' => [
                    ['type' => 'output_text', 'text' => 'Jawaban'], ['type' => 'output_text', 'text' => 'Sumber'],
                ]],
            ]]
            : ['type' => 'message', 'role' => 'assistant', 'stop_reason' => 'end_turn', 'content' => [
                ['type' => 'thinking', 'thinking' => 'private reasoning'],
                ['type' => 'text', 'text' => 'Jawaban'], ['type' => 'text', 'text' => 'Sumber'],
            ]];
    }

    #[DataProvider('providers')]
    public function test_transport_maps_neutral_history_and_only_exposes_text(string $provider, string $class, string $model, string $url): void
    {
        config(["services.{$provider}.api_key" => 'fake-provider-secret']);
        $options = [];
        Http::fake(function (Request $request, array $requestOptions) use ($provider, &$options) {
            $options = $requestOptions;
            return Http::response(self::body($provider));
        });
        $messages = [['role' => 'user', 'text' => 'Pertanyaan'], ['role' => 'assistant', 'text' => 'Lama'], ['role' => 'user', 'text' => 'Lanjutan']];
        $this->assertSame("Jawaban\nSumber", app($class)->chat($model, $messages));
        Http::assertSentCount(1);
        Http::assertSent(function (Request $r) use ($provider, $model, $url) {
            $this->assertSame($url, $r->url());
            $this->assertSame($model, $r['model']);
            $this->assertStringNotContainsString('fake-provider-secret', json_encode($r->data()));
            $this->assertSame([
                ['role' => 'user', 'content' => 'Pertanyaan'], ['role' => 'assistant', 'content' => 'Lama'], ['role' => 'user', 'content' => 'Lanjutan'],
            ], $r[$provider === 'openai' ? 'input' : 'messages']);
            if ($provider === 'openai') {
                $this->assertTrue($r->hasHeader('Authorization', 'Bearer fake-provider-secret'));
                $this->assertFalse($r['store']);
                $this->assertSame(2048, $r['max_output_tokens']);
                $this->assertFalse($r->hasHeader('x-api-key'));
            } else {
                $this->assertTrue($r->hasHeader('x-api-key', 'fake-provider-secret'));
                $this->assertTrue($r->hasHeader('anthropic-version', '2023-06-01'));
                $this->assertSame(2048, $r['max_tokens']);
                $this->assertFalse($r->hasHeader('Authorization'));
            }
            return true;
        });
        $this->assertSame(45, $options['timeout']);
        $this->assertFalse($options['allow_redirects']);
    }

    public static function failures(): array
    {
        $cases = [];
        foreach (self::providers() as [$provider, $class, $model]) {
            foreach ([400, 401, 403, 404, 429, 500, 503, 529] as $status) {
                $cases[] = [$provider, $class, $model, $status];
            }
        }
        return $cases;
    }

    #[DataProvider('failures')]
    public function test_http_errors_are_safe_and_never_retried(string $provider, string $class, string $model, int $status): void
    {
        config(["services.{$provider}.api_key" => 'fake-provider-secret']);
        Http::fake(['*' => Http::response(['error' => 'fake-provider-secret private body'], $status)]);
        foreach (['chat', 'test'] as $method) {
            try {
                app($class)->$method($model, []);
                $this->fail('Expected safe exception');
            } catch (AIProviderException $e) {
                $this->assertSame($status, $e->status);
                $this->assertSame($provider, $e->provider);
                $this->assertNull($e->getPrevious());
                $this->assertStringNotContainsString('fake-provider-secret', $e->getMessage().$e->previewMessage());
                $this->assertSame(match ($status) { 429 => 429, 503, 529 => 503, default => 502 }, $e->previewStatus());
            }
        }
        Http::assertSentCount(2);
    }

    public static function invalidBodies(): array
    {
        $cases = [];
        foreach (self::providers() as [$provider, $class, $model]) {
            foreach (['invalid json', [], ['unexpected' => true]] as $body) {
                $cases[] = [$provider, $class, $model, $body];
            }
            $body = self::body($provider);
            $body[$provider === 'openai' ? 'status' : 'stop_reason'] = $provider === 'openai' ? 'incomplete' : 'max_tokens';
            $cases[] = [$provider, $class, $model, $body];
            if ($provider === 'openai') {
                foreach ([[['type' => 'refusal', 'refusal' => 'No']], [['type' => 'output_text', 'text' => '']], [['type' => 'output_text', 'text' => []]]] as $content) {
                    $body = self::body($provider);
                    $body['output'][1]['content'] = $content;
                    $cases[] = [$provider, $class, $model, $body];
                }
                $body['output'] = [['type' => 'reasoning']];
            } else {
                foreach (['refusal', 'tool_use', 'pause_turn'] as $reason) {
                    $body = self::body($provider);
                    $body['stop_reason'] = $reason;
                    $cases[] = [$provider, $class, $model, $body];
                }
                $body = self::body($provider);
                $body['content'] = [['type' => 'thinking', 'thinking' => 'private']];
            }
            $cases[] = [$provider, $class, $model, $body];
        }
        return $cases;
    }

    #[DataProvider('invalidBodies')]
    public function test_incomplete_refused_or_malformed_responses_never_succeed(string $provider, string $class, string $model, mixed $body): void
    {
        config(["services.{$provider}.api_key" => 'fake-provider-secret']);
        Http::fake(['*' => Http::response($body)]);
        foreach (['chat', 'test'] as $method) {
            try {
                app($class)->$method($model, []);
                $this->fail('Expected invalid response');
            } catch (AIProviderException $e) {
                $this->assertSame('invalid_response', $e->category);
            }
        }
        Http::assertSentCount(2);
    }

    #[DataProvider('providers')]
    public function test_missing_key_and_network_error_are_safe(string $provider, string $class, string $model): void
    {
        config(["services.{$provider}.api_key" => ' ']);
        Http::fake();
        try {
            app($class)->test($model, []);
            $this->fail('Expected missing key');
        } catch (AIProviderException $e) {
            $this->assertSame('configuration', $e->category);
        }
        Http::assertNothingSent();
        config(["services.{$provider}.api_key" => 'fake-provider-secret']);
        $calls = 0;
        Http::fake(function () use (&$calls) {
            $calls++;
            throw new ConnectionException('private fake-provider-secret');
        });
        try {
            app($class)->chat($model, []);
            $this->fail('Expected network error');
        } catch (AIProviderException $e) {
            $this->assertSame(504, $e->previewStatus());
            $this->assertNull($e->getPrevious());
        }
        $this->assertSame(1, $calls);
    }
}
