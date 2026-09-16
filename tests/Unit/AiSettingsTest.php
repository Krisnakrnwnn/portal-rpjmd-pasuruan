<?php

namespace Tests\Unit;

use App\Services\AI\AiSettings;
use Tests\TestCase;

class AiSettingsTest extends TestCase
{
    public function test_catalog_limits_providers_and_models_without_loading_a_database(): void
    {
        $settings = app(AiSettings::class);
        $this->assertTrue($settings->allows('gemini', 'gemini-2.5-flash'));
        $this->assertTrue($settings->allows('gemini', 'gemini-2.5-pro'));
        foreach ([['gemini', 'gemini-embedding-001'], ['openai', 'gemini-2.5-pro'], [[], []], ['gemini', '../model']] as [$provider, $model]) {
            $this->assertFalse($settings->allows($provider, $model));
        }
        config(['ai.providers.unimplemented' => ['models' => ['example' => 'Example']]]);
        $this->assertFalse($settings->allows('unimplemented', 'example'));
    }
}
