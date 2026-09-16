<?php

namespace App\Http\Requests\Admin;

use App\Services\AI\AiSettings;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAiSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['Admin', 'Super Admin'], true);
    }

    protected function prepareForValidation(): void
    {
        // Compatibility for the existing Gemini-only save form.
        if (! $this->exists('provider')) {
            $this->merge(['provider' => 'gemini']);
        }
    }

    public function rules(AiSettings $settings): array
    {
        return $settings->rules($this->input('provider'));
    }

    public function messages(): array
    {
        return [
            'provider.*' => 'Provider AI harus Google Gemini.',
            'gemini_model.*' => 'Pilih model AI yang tersedia dalam daftar.',
        ];
    }
}
