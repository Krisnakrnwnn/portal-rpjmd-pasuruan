<?php

namespace App\Services\AI;

use App\Models\Activity;
use App\Models\Stat;
use App\Services\AI\Exceptions\AIProviderException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AiSettings
{
    public const RESERVED_KEYS = ['ai_provider', 'ai_model', 'gemini_model'];

    public function catalog(): array
    {
        // Only explicitly implemented providers are usable, even if config is extended.
        return array_intersect_key(config('ai.providers', []), array_flip(['gemini', 'openai', 'anthropic']));
    }

    public function models(mixed $provider): array
    {
        return is_string($provider) ? ($this->catalog()[$provider]['models'] ?? []) : [];
    }

    public function allows(mixed $provider, mixed $model): bool
    {
        return is_string($provider) && ($this->catalog()[$provider]['enabled'] ?? false)
            && is_string($model) && array_key_exists($model, $this->models($provider));
    }

    public function configured(string $provider): bool
    {
        if (! array_key_exists($provider, $this->catalog())) {
            return false;
        }
        $key = config("services.{$provider}.api_key");

        return is_string($key) && trim($key) !== '';
    }

    public function readiness(): array
    {
        return array_map(fn ($provider) => $this->configured($provider), array_combine(array_keys($this->catalog()), array_keys($this->catalog())));
    }

    public function rules(mixed $provider, string $field = 'model'): array
    {
        return [
            'provider' => ['required', 'string', Rule::in(array_keys(array_filter($this->catalog(), fn ($item) => $item['enabled'] ?? false)))],
            $field => ['required', 'string', 'max:100', Rule::in(array_keys($this->models($provider)))],
        ];
    }

    public function validateReadiness(string $provider): void
    {
        if (! $this->configured($provider) || ! $this->configured('gemini')) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'provider' => 'API key provider jawaban dan Gemini untuk pencarian dokumen harus dikonfigurasi sebelum Simpan.',
            ]);
        }
    }

    public function current(): array
    {
        // One uncached snapshot: every request observes the latest committed pair.
        $stored = Stat::whereIn('key', self::RESERVED_KEYS)->pluck('value', 'key');
        $neutral = $stored->has('ai_model');
        $provider = $stored['ai_provider'] ?? ($neutral ? null : config('ai.default_provider'));
        $model = $neutral ? $stored['ai_model'] : ($provider === 'gemini' ? ($stored['gemini_model'] ?? config('ai.default_model')) : null);
        $fallback = ! $this->allows($provider, $model);
        if ($fallback) {
            $provider = config('ai.default_provider');
            $model = config('ai.default_model');
            Log::warning('AI settings fallback', ['category' => 'invalid_stored_configuration']);
        }
        if (! $this->allows($provider, $model)) {
            throw new AIProviderException('configuration');
        }

        return [
            'provider' => $provider,
            'model' => $model,
            'provider_label' => $this->catalog()[$provider]['label'],
            'model_label' => $this->models($provider)[$model],
            'fallback' => $fallback,
            'default' => ! $neutral && ! $stored->has('gemini_model'),
            'configured' => $this->configured($provider),
        ];
    }

    public function save(string $provider, string $model): void
    {
        Validator::make(['provider' => $provider, 'model' => $model], $this->rules($provider))->validate();
        $this->validateReadiness($provider);

        DB::transaction(function () use ($provider, $model) {
            // Upsert a missing row first so concurrent saves also serialize on it.
            $now = now();
            Stat::upsert([
                ['key' => 'ai_provider', 'value' => config('ai.default_provider'), 'label' => 'Provider AI Chatbot', 'created_at' => $now, 'updated_at' => $now],
            ], ['key'], ['key']);
            Stat::where('key', 'ai_provider')->lockForUpdate()->firstOrFail();
            $before = $this->current();
            Stat::updateOrCreate(['key' => 'ai_provider'], ['value' => $provider, 'label' => 'Provider AI Chatbot']);
            Stat::updateOrCreate(['key' => 'ai_model'], ['value' => $model, 'label' => 'Model AI Chatbot']);
            if ($provider === 'gemini') {
                Stat::updateOrCreate(['key' => 'gemini_model'], ['value' => $model, 'label' => 'Model AI Chatbot']);
            }
            Activity::log('Setelan', 'Update', "Model AI Chatbot: {$before['provider']}/{$before['model']} -> {$provider}/{$model}".($before['fallback'] ? ' (sebelumnya memakai fallback)' : ''));
        });
    }
}
