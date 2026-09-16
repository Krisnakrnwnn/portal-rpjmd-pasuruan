<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Stat;
use App\Models\User;
use App\Services\AI\AiSettings;
use App\Services\AI\Exceptions\AIProviderException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AiModelSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gemini.api_key' => 'fake-settings-secret']);
        Http::preventStrayRequests();
    }

    public static function roles(): array
    {
        return [['Admin'], ['Super Admin']];
    }

    #[DataProvider('roles')]
    public function test_authorized_roles_see_save_and_reload_model(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);
        $response = $this->actingAs($user)->get('/admin/setelan')->assertOk()
            ->assertSee('Google Gemini')->assertSee('Model aktif')->assertSee('Gemini 2.5 Flash')
            ->assertSee('href="'.route('admin.setelan.index').'"', false)->assertDontSee('fake-settings-secret');
        if ($role === 'Admin' && getenv('AI_REVIEW') === '1') {
            $directory = storage_path('framework/testing/ai-review');
            if (! is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            file_put_contents($directory.'/setelan.html', $response->getContent());
        }
        $this->post('/admin/settings', ['provider' => 'gemini', 'gemini_model' => 'gemini-2.5-pro'])
            ->assertRedirect(route('admin.setelan.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('stats', ['key' => 'ai_provider', 'value' => 'gemini']);
        $this->assertDatabaseHas('stats', ['key' => 'gemini_model', 'value' => 'gemini-2.5-pro']);
        $this->assertDatabaseHas('activities', ['user_id' => $user->id, 'type' => 'Setelan', 'action' => 'Update']);
        $this->assertStringContainsString('gemini/gemini-2.5-flash -> gemini/gemini-2.5-pro', Activity::latest('id')->first()->description);
        $this->get('/admin/setelan')->assertViewHas('aiSettings', fn ($s) => $s['model'] === 'gemini-2.5-pro');
        Http::assertNothingSent();
    }

    public function test_guest_and_user_cannot_read_save_or_test(): void
    {
        $this->get('/admin/setelan')->assertRedirect(route('login'));
        foreach (['/admin/settings', '/admin/settings/test-model'] as $url) {
            $this->postJson($url, ['provider' => 'gemini', 'gemini_model' => 'gemini-2.5-pro'])->assertUnauthorized();
        }
        $this->actingAs(User::factory()->create(['role' => 'User']));
        $this->get('/admin/setelan')->assertRedirect('/');
        foreach (['/admin/settings', '/admin/settings/test-model'] as $url) {
            $this->postJson($url, ['provider' => 'gemini', 'gemini_model' => 'gemini-2.5-pro'])->assertRedirect('/');
        }
        $this->assertDatabaseHas('stats', ['key' => 'gemini_model', 'value' => 'gemini-2.5-flash']);
        Http::assertNothingSent();
    }

    public static function invalidSettings(): array
    {
        $cases = [];
        foreach (['typo', '', 'gemini-embedding-001', 'models/gemini-2.5-pro', '../gemini-2.5-pro', 'https://example.test/model', 'gemini-2.5-pro?key=bad', ['gemini-2.5-pro'], (object) ['model' => 'gemini-2.5-pro']] as $value) {
            $cases[] = [['provider' => 'gemini', 'gemini_model' => $value], 'gemini_model'];
        }
        foreach (['openai', '', ['gemini'], (object) ['name' => 'gemini']] as $value) {
            $cases[] = [['provider' => $value, 'gemini_model' => 'gemini-2.5-pro'], 'provider'];
        }

        return $cases;
    }

    #[DataProvider('invalidSettings')]
    public function test_invalid_settings_are_rejected_without_mutation(array $data, string $field): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        foreach (['/admin/settings', '/admin/settings/test-model'] as $url) {
            $this->postJson($url, $data)->assertUnprocessable()->assertJsonValidationErrors($field);
        }
        $this->assertDatabaseHas('stats', ['key' => 'gemini_model', 'value' => 'gemini-2.5-flash']);
        $this->assertDatabaseMissing('stats', ['key' => 'ai_provider']);
        $this->assertDatabaseCount('activities', 0);
        Http::assertNothingSent();
    }

    public function test_general_statistics_cannot_bypass_ai_settings_and_reject_entire_batch(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        Stat::create(['key' => 'hero_documents', 'value' => '1']);
        foreach (['gemini_model', 'ai_provider', 'GEMINI_MODEL', 'ai_provider '] as $key) {
            foreach (['stats' => '/admin/stats', 'hero_stats' => '/admin/hero-stats'] as $field => $url) {
                $this->postJson($url, [$field => ['hero_documents' => '2', $key => 'bad']])->assertUnprocessable();
                $this->assertDatabaseHas('stats', ['key' => 'hero_documents', 'value' => '1']);
            }
        }
        $this->assertDatabaseCount('activities', 0);
        $this->post('/admin/hero-stats', ['hero_stats' => ['hero_documents' => 3]])->assertSessionHas('success');
        $this->assertDatabaseHas('stats', ['key' => 'hero_documents', 'value' => '3']);
        $this->post('/admin/stats', ['stats' => ['hero_documents' => '4']])->assertSessionHas('success');
        $this->assertDatabaseHas('stats', ['key' => 'hero_documents', 'value' => '4']);
    }

    public function test_failed_audit_rolls_back_both_settings(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        Activity::creating(function () {
            throw new \RuntimeException('private storage detail');
        });
        try {
            $this->post('/admin/settings', ['provider' => 'gemini', 'gemini_model' => 'gemini-2.5-pro'])
                ->assertRedirect(route('admin.setelan.index'))->assertSessionHas('error')->assertDontSee('private storage detail');
            $this->assertDatabaseMissing('stats', ['key' => 'ai_provider']);
            $this->assertDatabaseHas('stats', ['key' => 'gemini_model', 'value' => 'gemini-2.5-flash']);
            $this->assertDatabaseCount('activities', 0);
        } finally {
            Activity::flushEventListeners();
        }
    }

    public function test_preview_uses_candidate_without_chat_or_settings_side_effects(): void
    {
        Http::fake(['*:generateContent' => Http::response(['candidates' => [['content' => ['parts' => [['text' => '<script>example</script>']]]]]])]);
        $history = [['role' => 'user', 'parts' => [['text' => 'Riwayat pribadi']]]];
        $before = Stat::orderBy('id')->get()->toArray();
        $this->actingAs(User::factory()->create(['role' => 'Admin']))->withSession(['chatbot_history' => $history]);
        $response = $this->postJson('/admin/settings/test-model', ['provider' => 'gemini', 'gemini_model' => 'gemini-2.5-pro']);
        $response->assertOk()->assertJsonPath('success', true)->assertJsonPath('model', 'gemini-2.5-pro')->assertCookieMissing('chat_session_id')->assertDontSee('fake-settings-secret');
        Http::assertSentCount(1);
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/gemini-2.5-pro:generateContent') && $r['contents'] === [['role' => 'user', 'parts' => [['text' => 'Jawab singkat dalam Bahasa Indonesia bahwa koneksi model berhasil.']]]]);
        $this->assertSame($before, Stat::orderBy('id')->get()->toArray());
        $this->assertSame($history, session('chatbot_history'));
        foreach (['chat_sessions', 'chat_messages', 'document_chunks', 'document_ingestions', 'activities'] as $table) {
            $this->assertDatabaseCount($table, 0);
        }
    }

    public function test_preview_error_is_safe_single_attempt_and_throttled(): void
    {
        Http::fake(['*' => Http::response(['error' => ['message' => 'fake-settings-secret raw provider detail']], 503)]);
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/admin/settings/test-model', ['provider' => 'gemini', 'gemini_model' => 'gemini-2.5-pro'])
                ->assertStatus(503)->assertJsonPath('success', false)->assertDontSee('fake-settings-secret')->assertDontSee('raw provider detail');
        }
        $this->postJson('/admin/settings/test-model', ['provider' => 'gemini', 'gemini_model' => 'gemini-2.5-pro'])->assertTooManyRequests();
        Http::assertSentCount(5);
    }

    public function test_csrf_is_required_for_save_and_preview(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        $this->app->bind(ValidateCsrfToken::class, fn ($app) => new class($app, $app['encrypter']) extends ValidateCsrfToken
        {
            protected function runningUnitTests()
            {
                return false;
            }
        });
        foreach (['/admin/settings', '/admin/settings/test-model'] as $url) {
            $this->postJson($url, ['provider' => 'gemini', 'gemini_model' => 'gemini-2.5-pro'])->assertStatus(419);
        }
        Http::assertNothingSent();
    }

    public function test_invalid_database_value_uses_visible_fallback_without_rewriting_it(): void
    {
        Stat::where('key', 'gemini_model')->update(['value' => '../invalid']);
        $this->actingAs(User::factory()->create(['role' => 'Admin']))->get('/admin/setelan')->assertOk()
            ->assertSee('Setelan tersimpan tidak valid')->assertDontSee('../invalid')
            ->assertViewHas('aiSettings', fn ($s) => $s['fallback'] && $s['model'] === 'gemini-2.5-flash');
        $this->assertDatabaseHas('stats', ['key' => 'gemini_model', 'value' => '../invalid']);
    }

    public function test_missing_settings_use_defaults_without_writing_database(): void
    {
        Stat::whereIn('key', AiSettings::RESERVED_KEYS)->delete();
        $active = app(AiSettings::class)->current();
        $this->assertSame('gemini-2.5-flash', $active['model']);
        $this->assertTrue($active['default']);
        $this->assertDatabaseCount('stats', 0);
    }

    public function test_invalid_default_fails_safely_when_no_valid_stored_model_exists(): void
    {
        Stat::where('key', 'gemini_model')->delete();
        config(['ai.default_model' => 'invalid']);
        $this->actingAs(User::factory()->create(['role' => 'Admin']))->get('/admin/setelan')->assertOk()->assertSee('Konfigurasi default tidak valid');
        $this->expectException(AIProviderException::class);
        app(AiSettings::class)->current();
    }

    public function test_database_read_failure_is_not_mistaken_for_missing_settings(): void
    {
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        Schema::drop('stats');
        $this->expectException(QueryException::class);
        app(AiSettings::class)->current();
    }

    public static function previewFailures(): array
    {
        return [
            [400, ['error' => 'private detail'], 502],
            [401, ['error' => 'private detail'], 502],
            [403, ['error' => 'private detail'], 502],
            [404, ['error' => 'private detail'], 502],
            [429, ['error' => 'private detail'], 429],
            [503, ['error' => 'private detail'], 503],
            [200, 'not json private detail', 502],
            [200, ['candidates' => []], 502],
            [200, ['promptFeedback' => ['blockReason' => 'SAFETY']], 502],
        ];
    }

    #[DataProvider('previewFailures')]
    public function test_preview_failure_contract_keeps_active_settings(int $status, mixed $body, int $expected): void
    {
        Http::fake(['*:generateContent' => Http::response($body, $status)]);
        $this->actingAs(User::factory()->create(['role' => 'Super Admin']))
            ->postJson('/admin/settings/test-model', ['provider' => 'gemini', 'gemini_model' => 'gemini-2.5-pro'])
            ->assertStatus($expected)->assertJsonPath('success', false)->assertJsonPath('model', 'gemini-2.5-pro')
            ->assertDontSee('private detail')->assertDontSee('fake-settings-secret');
        Http::assertSentCount(1);
        $this->assertDatabaseHas('stats', ['key' => 'gemini_model', 'value' => 'gemini-2.5-flash']);
        $this->assertDatabaseCount('chat_messages', 0);
        $this->assertDatabaseCount('activities', 0);
    }

    public function test_database_write_failure_rolls_back_provider_and_keeps_candidate(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        Stat::updating(function ($stat) {
            if ($stat->key === 'gemini_model') {
                throw new \RuntimeException('private database detail');
            }
        });
        try {
            $this->post('/admin/settings', ['provider' => 'gemini', 'gemini_model' => 'gemini-2.5-pro'])
                ->assertSessionHas('error')->assertSessionHasInput('gemini_model', 'gemini-2.5-pro');
            $this->assertDatabaseMissing('stats', ['key' => 'ai_provider']);
            $this->assertDatabaseHas('stats', ['key' => 'gemini_model', 'value' => 'gemini-2.5-flash']);
            $this->assertDatabaseCount('activities', 0);
            Http::assertNothingSent();
        } finally {
            Stat::flushEventListeners();
        }
    }
}
