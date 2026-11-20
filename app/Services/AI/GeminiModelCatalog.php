<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiModelCatalog
{
    /**
     * Cache key for the fetched model list.
     */
    private const CACHE_KEY = 'gemini_model_catalog';

    /**
     * Cache duration in seconds (8 hours).
     */
    private const CACHE_TTL = 28800;

    /**
     * API endpoint for listing models.
     */
    private const API_URL = 'https://generativelanguage.googleapis.com/v1beta/models';

    /**
     * Request timeout in seconds.
     */
    private const TIMEOUT = 5;

    /**
     * Page size for the models.list API.
     */
    private const PAGE_SIZE = 100;

    /**
     * Model name prefixes to exclude (not suitable for chat text generation).
     *
     * Filter rules:
     * 1. Only models whose supportedGenerationMethods includes "generateContent" are kept.
     * 2. Models matching any of these prefix patterns are excluded:
     *    - "embedding" — embedding models (e.g. text-embedding-004)
     *    - "aqa" — Attributed Question Answering models
     *    - "imagen" — image generation models
     *    - "gemini-embedding" — Gemini embedding variants
     * 3. Models whose name contains any of these substrings are excluded:
     *    - "tts" — text-to-speech / audio models
     *    - "image-generation" — image generation models
     *    - "learnlm" — LearnLM educational models (not standard chat)
     */
    private const EXCLUDED_PREFIXES = ['embedding', 'aqa', 'imagen', 'gemini-embedding'];
    private const EXCLUDED_SUBSTRINGS = ['tts', 'image-generation', 'learnlm'];

    /**
     * Get the available Gemini models as an associative array [id => label].
     *
     * Returns cached results when available, fetches from API otherwise,
     * and falls back to the static catalog on any failure.
     */
    public function list(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return $this->fetchFromApi() ?: $this->fallbackCatalog();
        });
    }

    /**
     * Fetch models from the Google API with pagination.
     *
     * @return array<string, string> Model ID => display name, or empty array on failure.
     */
    private function fetchFromApi(): array
    {
        $apiKey = config('services.gemini.api_key');

        if (! is_string($apiKey) || trim($apiKey) === '') {
            Log::warning('Gemini model catalog: API key not configured, using fallback catalog', [
                'category' => 'model_catalog_fallback',
            ]);

            return [];
        }

        try {
            $models = [];
            $pageToken = null;

            do {
                $query = ['pageSize' => self::PAGE_SIZE];
                if ($pageToken !== null) {
                    $query['pageToken'] = $pageToken;
                }

                $response = Http::withHeaders(['x-goog-api-key' => $apiKey])
                    ->timeout(self::TIMEOUT)
                    ->connectTimeout(self::TIMEOUT)
                    ->get(self::API_URL, $query);

                if (! $response->successful()) {
                    Log::warning('Gemini model catalog: API request failed, using fallback catalog', [
                        'category' => 'model_catalog_fallback',
                        'status' => $response->status(),
                    ]);

                    return [];
                }

                $data = $response->json();

                foreach ($data['models'] ?? [] as $model) {
                    $methods = $model['supportedGenerationMethods'] ?? [];

                    if (! in_array('generateContent', $methods, true)) {
                        continue;
                    }

                    // Extract the model ID from "models/xxx" format.
                    $name = $model['name'] ?? '';
                    $id = str_starts_with($name, 'models/') ? substr($name, 7) : $name;

                    if ($id === '' || $this->isExcluded($id)) {
                        continue;
                    }

                    $models[$id] = $model['displayName'] ?? $id;
                }

                $pageToken = $data['nextPageToken'] ?? null;
            } while ($pageToken !== null);

            if ($models === []) {
                Log::warning('Gemini model catalog: API returned no suitable models, using fallback catalog', [
                    'category' => 'model_catalog_fallback',
                ]);

                return [];
            }

            return $models;
        } catch (\Throwable $e) {
            Log::warning('Gemini model catalog: API request error, using fallback catalog', [
                'category' => 'model_catalog_fallback',
                'error_class' => get_class($e),
            ]);

            return [];
        }
    }

    /**
     * Check whether a model ID should be excluded based on prefix and substring rules.
     */
    private function isExcluded(string $id): bool
    {
        $lower = strtolower($id);

        foreach (self::EXCLUDED_PREFIXES as $prefix) {
            if (str_starts_with($lower, $prefix)) {
                return true;
            }
        }

        foreach (self::EXCLUDED_SUBSTRINGS as $substring) {
            if (str_contains($lower, $substring)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the static fallback catalog from config.
     *
     * @return array<string, string>
     */
    public function fallbackCatalog(): array
    {
        return config('ai.providers.gemini.fallback_models', []);
    }

    /**
     * Clear the cached model list so the next call to list() will re-fetch.
     */
    public function refresh(): array
    {
        Cache::forget(self::CACHE_KEY);

        return $this->list();
    }

    /**
     * Check whether a given model ID exists in the current catalog (dynamic or fallback).
     */
    public function has(string $modelId): bool
    {
        return array_key_exists($modelId, $this->list());
    }
}

