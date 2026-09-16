<?php

namespace Tests\Unit;

use App\Services\AI\Exceptions\AIProviderException;
use App\Services\AI\Providers\GeminiProvider;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GeminiProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gemini.api_key' => 'fake-provider-secret']);
        Http::preventStrayRequests();
        Sleep::fake();
    }

    public function test_neutral_messages_map_to_existing_payload_and_key_stays_out_of_url(): void
    {
        Http::fake(['*' => Http::response(['candidates' => [['content' => ['parts' => [['text' => 'Answer'], ['text' => 'ignored as before']]]]]])]);
        $reply = app(GeminiProvider::class)->chat('gemini-2.5-flash', [['role' => 'user', 'text' => 'Question'], ['role' => 'assistant', 'text' => 'Earlier answer']]);
        $this->assertSame('Answer', $reply);
        Http::assertSent(fn (Request $r) => $r->url() === 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent' && $r->hasHeader('x-goog-api-key', 'fake-provider-secret') && $r->data() === ['contents' => [
            ['role' => 'user', 'parts' => [['text' => 'Question']]], ['role' => 'model', 'parts' => [['text' => 'Earlier answer']]],
        ]]);
    }

    public static function errors(): array
    {
        return [[400, 1], [401, 1], [403, 1], [404, 1], [429, 3], [503, 3], [500, 1]];
    }

    #[DataProvider('errors')]
    public function test_public_retries_only_transient_http_errors(int $status, int $attempts): void
    {
        Http::fake(['*' => Http::response(['error' => ['message' => 'raw fake-provider-secret']], $status)]);
        try {
            app(GeminiProvider::class)->chat('gemini-2.5-flash', [['role' => 'user', 'text' => 'Question']]);
            $this->fail('Expected provider error');
        } catch (AIProviderException $e) {
            $this->assertSame($status, $e->status);
            $this->assertNull($e->getPrevious());
            $this->assertStringNotContainsString('fake-provider-secret', $e->getMessage().$e->previewMessage());
        }
        Http::assertSentCount($attempts);
        Sleep::assertSleptTimes($attempts === 3 ? 3 : 0);
    }

    public static function unusableResponses(): array
    {
        return [['not json'], [[]], [['candidates' => []]], [['promptFeedback' => ['blockReason' => 'SAFETY']]], [['candidates' => [['content' => ['parts' => [['text' => '']]]]]]]];
    }

    #[DataProvider('unusableResponses')]
    public function test_unusable_text_keeps_public_fallback_but_fails_preview(mixed $body): void
    {
        Http::fake(['*' => Http::response($body)]);
        $provider = app(GeminiProvider::class);
        $this->assertSame('Saya tidak dapat memberikan jawaban saat ini.', $provider->chat('gemini-2.5-flash', []));
        $this->expectException(AIProviderException::class);
        $provider->test('gemini-2.5-flash', []);
    }

    public function test_network_timeout_is_safe_and_never_retried(): void
    {
        $calls = 0;
        Http::fake(function () use (&$calls) {
            $calls++;
            throw new ConnectionException('URL?key=fake-provider-secret private prompt');
        });
        try {
            app(GeminiProvider::class)->test('gemini-2.5-flash', []);
            $this->fail('Expected connection error');
        } catch (AIProviderException $e) {
            $this->assertSame('connection', $e->category);
            $this->assertSame(504, $e->previewStatus());
            $this->assertNull($e->getPrevious());
            $this->assertStringNotContainsString('fake-provider-secret', $e->getMessage());
        }
        $this->assertSame(1, $calls);
    }

    public function test_missing_key_never_calls_provider(): void
    {
        config(['services.gemini.api_key' => null]);
        Http::fake();
        try {
            app(GeminiProvider::class)->chat('gemini-2.5-flash', []);
            $this->fail('Expected configuration error');
        } catch (AIProviderException $e) {
            $this->assertSame('configuration', $e->category);
        }
        Http::assertNothingSent();
    }
}
