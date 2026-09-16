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
    public const RESERVED_KEYS = ['ai_provider', 'gemini_model'];

    public function catalog(): array
    {
        // Only explicitly implemented providers are usable, even if config is extended.
        return array_intersect_key(config('ai.providers', []), ['gemini' => true]);
    }

    public function models(mixed $provider): array
    {
        return is_string($provider) ? ($this->catalog()[$provider]['models'] ?? []) : [];
    }

    public function allows(mixed $provider, mixed $model): bool
    {
        return is_string($model) && array_key_exists($model, $this->models($provider));
    }

    public function rules(mixed $provider): array
    {
        return [
            'provider' => ['required', 'string', Rule::in(array_keys($this->catalog()))],
            'gemini_model' => ['required', 'string', 'max:100', Rule::in(array_keys($this->models($provider)))],
        ];
    }

    public function current(): array
    {
        // One uncached snapshot: every request observes the latest committed pair.
        $stored = Stat::whereIn('key', self::RESERVED_KEYS)->pluck('value', 'key');
        $provider = $stored['ai_provider'] ?? config('ai.default_provider');
        $model = $stored['gemini_model'] ?? config('ai.default_model');
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
            'default' => ! $stored->has('gemini_model'),
        ];
    }

    public function save(string $provider, string $model): void
    {
        Validator::make(['provider' => $provider, 'gemini_model' => $model], $this->rules($provider))->validate();

        DB::transaction(function () use ($provider, $model) {
            // Upsert a missing row first so concurrent saves also serialize on it.
            $now = now();
            Stat::upsert([
                ['key' => 'ai_provider', 'value' => $provider, 'label' => 'Provider AI Chatbot', 'created_at' => $now, 'updated_at' => $now],
            ], ['key'], ['key']);
            Stat::where('key', 'ai_provider')->lockForUpdate()->firstOrFail();
            $before = $this->current();
            Stat::updateOrCreate(['key' => 'ai_provider'], ['value' => $provider, 'label' => 'Provider AI Chatbot']);
            Stat::updateOrCreate(['key' => 'gemini_model'], ['value' => $model, 'label' => 'Model AI Chatbot']);
            Activity::log('Setelan', 'Update', "Model AI Chatbot: {$before['provider']}/{$before['model']} -> {$provider}/{$model}".($before['fallback'] ? ' (sebelumnya memakai fallback)' : ''));
        });
    }
}
