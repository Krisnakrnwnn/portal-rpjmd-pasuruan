<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Stat;
use App\Models\User;
use App\Services\AI\AiSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\Unit\MultiProviderTest;

class AiMultiProviderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['gemini', 'openai', 'anthropic'] as $provider) {
            config(["services.{$provider}.api_key" => "fake-{$provider}-secret"]);
        }
    }

    public static function choices(): array
    {
        return [
            ['Admin', 'gemini', 'gemini-2.5-flash'],
            ['Super Admin', 'gemini', 'gemini-2.5-flash'],
            ['Admin', 'openai', 'gpt-4.1-mini-2025-04-14'],
            ['Super Admin', 'openai', 'gpt-4.1-mini-2025-04-14'],
            ['Admin', 'anthropic', 'claude-sonnet-4-6'],
            ['Super Admin', 'anthropic', 'claude-sonnet-4-6'],
        ];
    }

    #[DataProvider('choices')]
    public function test_save_reload_and_preview_are_isolated(string $role, string $provider, string $model): void
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);
        $this->get('/admin/setelan')->assertOk()->assertSee('OpenAI (GPT)')->assertSee('Anthropic (Claude)')
            ->assertDontSee('fake-openai-secret')->assertDontSee('fake-anthropic-secret');
        $this->post('/admin/settings', compact('provider', 'model'))->assertSessionHas('success');
        Http::assertNothingSent();
        $this->assertDatabaseHas('stats', ['key' => 'ai_provider', 'value' => $provider]);
        $this->assertDatabaseHas('stats', ['key' => 'ai_model', 'value' => $model]);
        $this->assertDatabaseHas('stats', ['key' => 'gemini_model', 'value' => 'gemini-2.5-flash']);
        $this->assertDatabaseHas('activities', ['user_id' => $user->id, 'type' => 'Setelan']);
        $this->get('/admin/setelan')->assertViewHas('aiSettings', fn ($s) => $s['provider'] === $provider && $s['model'] === $model);
        $this->assertSame($model, (new AiSettings)->current()['model']);
        $before = Stat::orderBy('id')->get()->toArray();
        $body = $provider === 'gemini' ? ['candidates' => [['content' => ['parts' => [['text' => 'Jawaban']]]]]] : MultiProviderTest::body($provider);
        Http::fake(['*' => Http::response($body)]);
        $this->withSession(['chatbot_history' => ['sentinel']])->postJson('/admin/settings/test-model', compact('provider', 'model'))
            ->assertOk()->assertJsonPath('success', true)->assertJsonPath('provider', $provider)->assertCookieMissing('chat_session_id');
        Http::assertSentCount(1);
        $this->assertSame($before, Stat::orderBy('id')->get()->toArray());
        $this->assertSame(['sentinel'], session('chatbot_history'));
        foreach (['chat_sessions', 'chat_messages', 'document_chunks', 'document_ingestions'] as $table) {
            $this->assertDatabaseCount($table, 0);
        }
        $this->assertDatabaseCount('activities', 1);
    }

    public static function invalidPairs(): array
    {
        return [
            [['provider' => 'openai', 'model' => 'gemini-2.5-flash']],
            [['provider' => 'gemini', 'model' => 'claude-sonnet-4-6']],
            [['provider' => 'other', 'model' => 'unknown']],
            [['provider' => ['openai'], 'model' => []]],
            [['provider' => 'openai', 'model' => 'https://example.test']],
            [['model' => 'gemini-2.5-flash']],
            [['provider' => 'gemini', 'model' => 'gemini-2.5-pro', 'gemini_model' => 'gemini-2.5-flash']],
            [['provider' => 'openai', 'gemini_model' => 'gpt-4.1-mini-2025-04-14']],
        ];
    }

    #[DataProvider('invalidPairs')]
    public function test_invalid_pairs_and_ambiguous_aliases_never_mutate_or_call_provider(array $payload): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        foreach (['/admin/settings', '/admin/settings/test-model'] as $url) {
            $this->postJson($url, $payload)->assertUnprocessable();
        }
        $this->assertDatabaseMissing('stats', ['key' => 'ai_model']);
        $this->assertDatabaseCount('activities', 0);
        Http::assertNothingSent();
    }

    public function test_missing_keys_block_save_but_preview_does_not_require_embedding(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        $payload = ['provider' => 'openai', 'model' => 'gpt-4.1-mini-2025-04-14'];
        config(['services.openai.api_key' => null]);
        $this->postJson('/admin/settings', $payload)->assertUnprocessable()->assertJsonValidationErrors('provider');
        $this->postJson('/admin/settings/test-model', $payload)->assertStatus(503)->assertJsonPath('error_code', 'configuration');
        Http::assertNothingSent();
        config(['services.openai.api_key' => 'fake-openai-secret', 'services.gemini.api_key' => null]);
        $this->postJson('/admin/settings', $payload)->assertUnprocessable();
        Http::fake(['https://api.openai.com/v1/responses' => Http::response(MultiProviderTest::body('openai'))]);
        $this->postJson('/admin/settings/test-model', $payload)->assertOk();
        Http::assertSentCount(1);
        $this->assertDatabaseMissing('stats', ['key' => 'ai_model']);
    }

    public function test_legacy_transition_neutral_precedence_and_rollback_to_gemini(): void
    {
        $settings = app(AiSettings::class);
        $this->assertSame('gemini-2.5-flash', $settings->current()['model']);
        $settings->save('openai', 'gpt-4.1-mini-2025-04-14');
        $this->assertSame('gpt-4.1-mini-2025-04-14', $settings->current()['model']);
        $this->assertDatabaseHas('stats', ['key' => 'gemini_model', 'value' => 'gemini-2.5-flash']);
        $settings->save('gemini', 'gemini-2.5-pro');
        foreach (['ai_model', 'gemini_model'] as $key) {
            $this->assertDatabaseHas('stats', ['key' => $key, 'value' => 'gemini-2.5-pro']);
        }
        $this->assertSame('gemini-2.5-pro', $settings->current()['model']);
    }

    public static function partialSettings(): array
    {
        return [
            [['ai_model' => 'gemini-2.5-pro']],
            [['ai_provider' => 'openai']],
            [['ai_provider' => '', 'ai_model' => 'gemini-2.5-flash']],
            [['ai_provider' => 'openai', 'ai_model' => 'gemini-2.5-flash']],
        ];
    }

    #[DataProvider('partialSettings')]
    public function test_partial_or_invalid_pair_uses_visible_fallback_without_writes(array $stored): void
    {
        foreach ($stored as $key => $value) {
            Stat::updateOrCreate(compact('key'), compact('value'));
        }
        $before = Stat::orderBy('id')->get()->toArray();
        $current = app(AiSettings::class)->current();
        $this->assertTrue($current['fallback']);
        $this->assertSame('gemini', $current['provider']);
        $this->assertSame($before, Stat::orderBy('id')->get()->toArray());
    }

    public function test_audit_failure_rolls_back_neutral_pair(): void
    {
        $settings = app(AiSettings::class);
        $settings->save('openai', 'gpt-4.1-mini-2025-04-14');
        Activity::creating(fn () => throw new \RuntimeException('private'));
        try {
            $this->actingAs(User::factory()->create(['role' => 'Admin']))
                ->post('/admin/settings', ['provider' => 'anthropic', 'model' => 'claude-sonnet-4-6'])->assertSessionHas('error');
            $this->assertSame('openai', $settings->current()['provider']);
            $this->assertSame('gpt-4.1-mini-2025-04-14', $settings->current()['model']);
            $this->assertDatabaseCount('activities', 1);
        } finally {
            Activity::flushEventListeners();
        }
    }

    public function test_chat_switches_generation_only_and_retains_history(): void
    {
        Http::fake([
            '*:embedContent' => Http::response(['embedding' => ['values' => [1, 0]]]),
            '*:generateContent' => Http::response(['candidates' => [['content' => ['parts' => [['text' => 'Gemini']]]]]]),
            'https://api.openai.com/v1/responses' => Http::response(MultiProviderTest::body('openai')),
            'https://api.anthropic.com/v1/messages' => Http::response(MultiProviderTest::body('anthropic')),
        ]);
        $this->postJson('/api/chat', ['message' => 'Pertama'])->assertOk();
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        foreach ([['openai', 'gpt-4.1-mini-2025-04-14'], ['anthropic', 'claude-sonnet-4-6']] as [$provider, $model]) {
            $this->post('/admin/settings', compact('provider', 'model'))->assertSessionHas('success');
            $this->postJson('/api/chat', ['message' => 'Lanjutan', 'provider' => 'evil', 'model' => 'override'])->assertOk()->assertJsonPath('reply', "Jawaban\nSumber");
        }
        Http::assertSentCount(6);
        Http::assertSent(fn (Request $r) => $r->url() === 'https://api.anthropic.com/v1/messages'
            && $r['messages'][0] === ['role' => 'user', 'content' => 'Pertama']
            && $r['messages'][1] === ['role' => 'assistant', 'content' => 'Gemini']
            && $r['messages'][3] === ['role' => 'assistant', 'content' => "Jawaban\nSumber"]);
        $this->assertDatabaseCount('chat_messages', 6);
        $this->assertDatabaseCount('document_chunks', 0);
        $this->assertDatabaseCount('document_ingestions', 0);
    }

    public function test_preview_ignores_overrides_and_logs_only_safe_error_metadata(): void
    {
        Log::spy();
        Http::fake(['https://api.openai.com/v1/responses' => Http::response(['error' => 'private fake-openai-secret'], 403)]);
        $response = $this->actingAs(User::factory()->create(['role' => 'Admin']))->postJson('/admin/settings/test-model', [
            'provider' => 'openai', 'model' => 'gpt-4.1-mini-2025-04-14',
            'api_key' => 'override', 'url' => 'https://evil.test', 'prompt' => 'private override', 'context' => 'private context',
        ])->assertStatus(502)->assertJsonPath('error_code', 'access_denied')->assertJsonStructure(['request_id'])
            ->assertDontSee('private')->assertDontSee('fake-openai-secret');
        Http::assertSent(fn (Request $r) => $r->hasHeader('Authorization', 'Bearer fake-openai-secret')
            && $r['input'] === [['role' => 'user', 'content' => 'Jawab singkat dalam Bahasa Indonesia bahwa koneksi model berhasil.']]);
        Log::shouldHaveReceived('warning')->once()->withArgs(fn ($message, $context) => $message === 'AI generation failed'
            && $context['provider'] === 'openai' && $context['status'] === 403 && $context['request_id'] === $response->json('request_id')
            && ! str_contains(json_encode($context), 'private') && ! str_contains(json_encode($context), 'secret'));
    }
}
