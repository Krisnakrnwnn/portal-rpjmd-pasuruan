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
        if (! $this->exists('provider') && $this->exists('gemini_model') && ! $this->exists('model')) {
            $this->merge(['provider' => 'gemini']);
        }
    }

    public function rules(AiSettings $settings): array
    {
        return $settings->rules($this->input('provider'), $this->modelField());
    }

    public function modelField(): string
    {
        return $this->exists('gemini_model') && ! $this->exists('model') ? 'gemini_model' : 'model';
    }

    public function selectedModel(): string
    {
        return $this->validated($this->modelField());
    }

    protected function requiresEmbedding(): bool
    {
        return true;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->exists('gemini_model') && ($this->exists('model') || $this->input('provider') !== 'gemini')) {
                $validator->errors()->add('gemini_model', 'Field gemini_model hanya untuk form Gemini lama tanpa field model.');
            }
            if (! $validator->errors()->isEmpty() || ! $this->requiresEmbedding()) {
                return;
            }
            $settings = app(AiSettings::class);
            if (! $settings->configured($this->input('provider')) || ! $settings->configured('gemini')) {
                $validator->errors()->add('provider', 'API key provider jawaban dan Gemini untuk pencarian dokumen harus dikonfigurasi sebelum Simpan.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'provider.*' => 'Pilih provider AI yang tersedia dalam daftar.',
            'model.*' => 'Pilih model AI yang tersedia untuk provider ini.',
            'gemini_model.*' => 'Pilih model AI yang tersedia dalam daftar.',
        ];
    }
}
