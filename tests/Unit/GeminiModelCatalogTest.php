<?php

namespace Tests\Unit;

use App\Services\AI\GeminiModelCatalog;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class GeminiModelCatalogTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['services.gemini.api_key' => 'fake-test-gemini-key']);
        Http::preventStrayRequests();
    }

    public function test_only_generate_content_models_are_included(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models*' => Http::response([
                'models' => [
                    [
                        'name' => 'models/gemini-2.0-flash',
                        'displayName' => 'Gemini 2.0 Flash',
                        'supportedGenerationMethods' => ['generateContent', 'countTokens'],
                    ],
                    [
                        'name' => 'models/text-embedding-004',
                        'displayName' => 'Text Embedding 004',
                        'supportedGenerationMethods' => ['embedContent'],
                    ],
                    [
                        'name' => 'models/gemini-unsupported',
                        'displayName' => 'Unsupported Model',
                        'supportedGenerationMethods' => ['somethingElse'],
                    ],
                ],
            ]),
        ]);

        $catalog = new GeminiModelCatalog;
        $models = $catalog->list();

        $this->assertArrayHasKey('gemini-2.0-flash', $models);
        $this->assertArrayNotHasKey('text-embedding-004', $models);
        $this->assertArrayNotHasKey('gemini-unsupported', $models);
    }

    public function test_name_is_mapped_to_id_and_display_name_used_as_label(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models*' => Http::response([
                'models' => [
                    [
                        'name' => 'models/gemini-2.5-flash',
                        'displayName' => 'Gemini 2.5 Flash Custom Label',
                        'supportedGenerationMethods' => ['generateContent'],
                    ],
                    [
                        'name' => 'models/gemini-2.5-pro',
                        'displayName' => 'Gemini 2.5 Pro Custom Label',
                        'supportedGenerationMethods' => ['generateContent'],
                    ],
                ],
            ]),
        ]);

        $catalog = new GeminiModelCatalog;
        $models = $catalog->list();

        $this->assertSame('Gemini 2.5 Flash Custom Label', $models['gemini-2.5-flash']);
        $this->assertSame('Gemini 2.5 Pro Custom Label', $models['gemini-2.5-pro']);
    }

    public function test_non_chat_models_are_filtered_out(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models*' => Http::response([
                'models' => [
                    [
                        'name' => 'models/gemini-2.0-flash',
                        'displayName' => 'Gemini 2.0 Flash',
                        'supportedGenerationMethods' => ['generateContent'],
                    ],
                    [
                        'name' => 'models/imagen-3.0-generate-002',
                        'displayName' => 'Imagen 3',
                        'supportedGenerationMethods' => ['generateContent'],
                    ],
                    [
                        'name' => 'models/gemini-embedding-001',
                        'displayName' => 'Gemini Embedding',
                        'supportedGenerationMethods' => ['generateContent'],
                    ],
                    [
                        'name' => 'models/aqa',
                        'displayName' => 'Attributed QA',
                        'supportedGenerationMethods' => ['generateContent'],
                    ],
                    [
                        'name' => 'models/gemini-2.0-flash-exp-tts',
                        'displayName' => 'Gemini TTS',
                        'supportedGenerationMethods' => ['generateContent'],
                    ],
                    [
                        'name' => 'models/learnlm-1.5-pro-experimental',
                        'displayName' => 'LearnLM Experimental',
                        'supportedGenerationMethods' => ['generateContent'],
                    ],
                ],
            ]),
        ]);

        $catalog = new GeminiModelCatalog;
        $models = $catalog->list();

        $this->assertArrayHasKey('gemini-2.0-flash', $models);
        $this->assertArrayNotHasKey('imagen-3.0-generate-002', $models);
        $this->assertArrayNotHasKey('gemini-embedding-001', $models);
        $this->assertArrayNotHasKey('aqa', $models);
        $this->assertArrayNotHasKey('gemini-2.0-flash-exp-tts', $models);
        $this->assertArrayNotHasKey('learnlm-1.5-pro-experimental', $models);
    }

    public function test_fallback_when_api_key_is_empty(): void
    {
        Log::spy();
        config(['services.gemini.api_key' => '']);

        $catalog = new GeminiModelCatalog;
        $models = $catalog->list();

        $fallback = config('ai.providers.gemini.fallback_models');
        $this->assertSame($fallback, $models);
        Http::assertNothingSent();

        Log::shouldHaveReceived('warning')->once()->withArgs(function ($msg, $context) {
            return str_contains($msg, 'API key not configured')
                && ! str_contains(json_encode($context), 'fake');
        });
    }

    public function test_fallback_when_api_returns_http_error(): void
    {
        Log::spy();
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models*' => Http::response(['error' => 'quota exceeded'], 429),
        ]);

        $catalog = new GeminiModelCatalog;
        $models = $catalog->list();

        $fallback = config('ai.providers.gemini.fallback_models');
        $this->assertSame($fallback, $models);

        Log::shouldHaveReceived('warning')->once()->withArgs(function ($msg, $context) {
            return str_contains($msg, 'API request failed')
                && ($context['status'] ?? null) === 429
                && ! str_contains(json_encode($context), 'fake-test-gemini-key');
        });
    }

    public function test_fallback_when_api_request_times_out(): void
    {
        Log::spy();
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models*' => fn () => throw new ConnectionException('Connection timed out'),
        ]);

        $catalog = new GeminiModelCatalog;
        $models = $catalog->list();

        $fallback = config('ai.providers.gemini.fallback_models');
        $this->assertSame($fallback, $models);

        Log::shouldHaveReceived('warning')->once()->withArgs(function ($msg, $context) {
            return str_contains($msg, 'API request error')
                && ! str_contains(json_encode($context), 'fake-test-gemini-key');
        });
    }

    public function test_fallback_when_api_returns_empty_models(): void
    {
        Log::spy();
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models*' => Http::response(['models' => []]),
        ]);

        $catalog = new GeminiModelCatalog;
        $models = $catalog->list();

        $fallback = config('ai.providers.gemini.fallback_models');
        $this->assertSame($fallback, $models);

        Log::shouldHaveReceived('warning')->once()->withArgs(function ($msg, $context) {
            return str_contains($msg, 'no suitable models');
        });
    }

    public function test_cache_is_used_on_subsequent_calls(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models*' => Http::response([
                'models' => [
                    [
                        'name' => 'models/gemini-2.0-flash',
                        'displayName' => 'Gemini 2.0 Flash',
                        'supportedGenerationMethods' => ['generateContent'],
                    ],
                ],
            ]),
        ]);

        $catalog = new GeminiModelCatalog;

        $first = $catalog->list();
        $second = $catalog->list();

        $this->assertSame($first, $second);
        Http::assertSentCount(1);
    }

    public function test_refresh_clears_cache_and_calls_api_again(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models*' => Http::sequence()
                ->push([
                    'models' => [
                        [
                            'name' => 'models/gemini-2.0-flash',
                            'displayName' => 'Gemini 2.0 Flash Initial',
                            'supportedGenerationMethods' => ['generateContent'],
                        ],
                    ],
                ])
                ->push([
                    'models' => [
                        [
                            'name' => 'models/gemini-2.0-flash',
                            'displayName' => 'Gemini 2.0 Flash Refreshed',
                            'supportedGenerationMethods' => ['generateContent'],
                        ],
                    ],
                ]),
        ]);

        $catalog = new GeminiModelCatalog;

        $first = $catalog->list();
        $this->assertSame('Gemini 2.0 Flash Initial', $first['gemini-2.0-flash']);
        Http::assertSentCount(1);

        $refreshed = $catalog->refresh();
        $this->assertSame('Gemini 2.0 Flash Refreshed', $refreshed['gemini-2.0-flash']);
        Http::assertSentCount(2);
    }

    public function test_api_key_sent_via_header_not_query_string(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models*' => Http::response([
                'models' => [
                    [
                        'name' => 'models/gemini-2.0-flash',
                        'displayName' => 'Gemini 2.0 Flash',
                        'supportedGenerationMethods' => ['generateContent'],
                    ],
                ],
            ]),
        ]);

        $catalog = new GeminiModelCatalog;
        $catalog->list();

        Http::assertSent(function (Request $request) {
            $this->assertSame('fake-test-gemini-key', $request->header('x-goog-api-key')[0] ?? null);
            $this->assertStringNotContainsString('key=', $request->url());

            return true;
        });
    }

    public function test_pagination_follows_next_page_token(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models*' => Http::sequence()
                ->push([
                    'models' => [
                        [
                            'name' => 'models/gemini-page-1',
                            'displayName' => 'Gemini Page 1',
                            'supportedGenerationMethods' => ['generateContent'],
                        ],
                    ],
                    'nextPageToken' => 'token-page-2',
                ])
                ->push([
                    'models' => [
                        [
                            'name' => 'models/gemini-page-2',
                            'displayName' => 'Gemini Page 2',
                            'supportedGenerationMethods' => ['generateContent'],
                        ],
                    ],
                ]),
        ]);

        $catalog = new GeminiModelCatalog;
        $models = $catalog->list();

        $this->assertArrayHasKey('gemini-page-1', $models);
        $this->assertArrayHasKey('gemini-page-2', $models);
        Http::assertSentCount(2);
    }
}

