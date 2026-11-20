<?php

namespace Tests\Feature;

use App\Models\Stat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiModelCatalogFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config([
            'services.gemini.api_key' => 'fake-test-gemini-key',
            'services.openai.api_key' => 'fake-test-openai-key',
            'services.anthropic.api_key' => null,
        ]);
        Http::preventStrayRequests();
    }

    public function test_validation_rejects_model_not_in_catalog(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        // Model yang sama sekali tidak ada di daftar dinamis atau fallback
        $response = $this->actingAs($admin)->postJson('/admin/settings', [
            'provider' => 'gemini',
            'model' => 'gemini-non-existent-999',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('model');
    }

    public function test_validation_accepts_model_from_dynamic_api_catalog(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        // Fake Google models API returning a new dynamic model
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models*' => Http::response([
                'models' => [
                    [
                        'name' => 'models/gemini-2.0-flash-new',
                        'displayName' => 'Gemini 2.0 Flash New',
                        'supportedGenerationMethods' => ['generateContent'],
                    ],
                ],
            ]),
        ]);

        $response = $this->actingAs($admin)->post('/admin/settings', [
            'provider' => 'gemini',
            'model' => 'gemini-2.0-flash-new',
        ]);

        $response->assertRedirect(route('admin.setelan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('stats', ['key' => 'ai_model', 'value' => 'gemini-2.0-flash-new']);
        $this->assertDatabaseHas('stats', ['key' => 'gemini_model', 'value' => 'gemini-2.0-flash-new']);
    }

    public function test_validation_accepts_model_from_fallback_catalog_when_api_fails(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        // API fails, should fallback to static catalog which has gemini-2.0-flash
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models*' => Http::response(null, 500),
        ]);

        $response = $this->actingAs($admin)->post('/admin/settings', [
            'provider' => 'gemini',
            'model' => 'gemini-2.0-flash',
        ]);

        $response->assertRedirect(route('admin.setelan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('stats', ['key' => 'ai_model', 'value' => 'gemini-2.0-flash']);
    }

    public function test_stale_active_model_does_not_break_settings_page(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        // Set DB to a model that is completely obsolete / not in catalog
        Stat::updateOrCreate(['key' => 'ai_provider'], ['value' => 'gemini', 'label' => 'Provider AI Chatbot']);
        Stat::updateOrCreate(['key' => 'ai_model'], ['value' => 'gemini-1.0-deprecated-old', 'label' => 'Model AI Chatbot']);
        Stat::updateOrCreate(['key' => 'gemini_model'], ['value' => 'gemini-1.0-deprecated-old', 'label' => 'Model AI Chatbot']);

        // Dynamic API returns standard catalog without the deprecated model
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

        $response = $this->actingAs($admin)->get('/admin/setelan');

        // Page loads successfully (no 500 error)
        $response->assertOk();
        $response->assertViewHas('staleModel', 'gemini-1.0-deprecated-old');
        // Marked clearly that model is not available
        $response->assertSee('gemini-1.0-deprecated-old');
        $response->assertSee('tidak tersedia');

        // Database value is NOT overwritten by visiting the page
        $this->assertDatabaseHas('stats', ['key' => 'ai_model', 'value' => 'gemini-1.0-deprecated-old']);
        $this->assertDatabaseHas('stats', ['key' => 'gemini_model', 'value' => 'gemini-1.0-deprecated-old']);
    }

    public function test_refresh_models_endpoint_refreshes_cache_for_admin(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models*' => Http::response([
                'models' => [
                    [
                        'name' => 'models/gemini-refreshed-model',
                        'displayName' => 'Gemini Refreshed Model',
                        'supportedGenerationMethods' => ['generateContent'],
                    ],
                ],
            ]),
        ]);

        $response = $this->actingAs($admin)->postJson('/admin/settings/refresh-models');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure(['models' => ['gemini-refreshed-model']]);
    }

    public function test_refresh_models_endpoint_is_forbidden_for_guest_and_user_role(): void
    {
        // Guest
        $this->postJson('/admin/settings/refresh-models')->assertUnauthorized();

        // Regular User role
        $user = User::factory()->create(['role' => 'User']);
        $this->actingAs($user)->postJson('/admin/settings/refresh-models')->assertRedirect('/');
    }
}

