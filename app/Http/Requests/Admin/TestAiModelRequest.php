<?php

namespace App\Http\Requests\Admin;

use App\Services\AI\AiSettings;

class TestAiModelRequest extends UpdateAiSettingsRequest
{
    // The new endpoint has no legacy clients: provider must be supplied explicitly.
    protected function prepareForValidation(): void {}

    public function rules(AiSettings $settings): array
    {
        return $settings->rules($this->input('provider'), $this->modelField());
    }

    protected function requiresEmbedding(): bool
    {
        return false;
    }
}
