<?php

namespace App\Services\AI\Contracts;

interface AIProviderInterface
{
    /** @param array<int, array{role: 'user'|'assistant', text: string}> $messages */
    public function chat(string $model, array $messages): string;

    /** A single attempt; an unusable response must fail the test. */
    public function test(string $model, array $messages): string;
}
