<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\DocumentChunk;
use App\Models\User;
use App\Services\AI\AIManager;
use App\Services\AI\Exceptions\AIProviderException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;

class PriviaConversationService
{
    public function __construct(private AIManager $ai) {}

    public function send(User $user, ?Conversation $conversation, string $content): array
    {
        if (! $conversation) {
            $conversation = $user->conversations()->create([
                'title' => Str::limit(trim($content), 70, '…'),
            ]);
        }

        $message = $conversation->messages()->create([
            'role' => 'user',
            'content' => trim($content),
        ]);

        return $this->complete($conversation, $message);
    }

    public function retry(ConversationMessage $message): array
    {
        return $this->complete($message->conversation, $message);
    }

    private function complete(Conversation $conversation, ConversationMessage $userMessage): array
    {
        $messages = $conversation->messages()
            ->where('id', '<=', $userMessage->id)
            ->orderBy('id')
            ->get()
            ->map(fn (ConversationMessage $item) => [
                'role' => $item->role === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $item->content]],
            ])
            ->slice(-8)
            ->values()
            ->all();

        [$context, $sources] = $this->retrieve($userMessage->content);
        $greeting = $this->greeting();
        $prompt = $this->systemPrompt($greeting)."\n\n{$context}\n\nPertanyaan Baru Warga: {$userMessage->content}";
        $reply = $this->ai->chat($messages, $prompt);

        $assistant = $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $reply,
            'metadata' => $sources === [] ? null : ['sources' => $sources],
        ]);
        $conversation->touch();

        return compact('conversation', 'userMessage', 'assistant');
    }

    private function retrieve(string $question): array
    {
        $apiKey = config('services.gemini.api_key');
        if (! is_string($apiKey) || trim($apiKey) === '') {
            throw new AIProviderException('configuration');
        }

        $embeddingResponse = null;
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $embeddingResponse = Http::timeout(30)
                ->withHeaders(['x-goog-api-key' => $apiKey])
                ->withOptions(['allow_redirects' => false])
                ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-embedding-001:embedContent', [
                    'model' => 'models/gemini-embedding-001',
                    'content' => ['parts' => [['text' => $question]]],
                ]);

            if ($embeddingResponse->successful()) {
                break;
            }

            if ($embeddingResponse->status() !== 429 || $attempt === 3) {
                throw new AIProviderException('connection', $embeddingResponse->status());
            }

            Sleep::for(1)->seconds();
        }

        $embedding = $embeddingResponse?->json('embedding.values');
        if (! is_array($embedding)) {
            throw new AIProviderException('invalid_response');
        }

        $ranked = [];
        DocumentChunk::query()->chunkById(200, function ($chunks) use (&$ranked, $embedding) {
            foreach ($chunks as $chunk) {
                if (! is_array($chunk->embedding)) {
                    continue;
                }

                $score = $this->cosineSimilarity($embedding, $chunk->embedding);
                if ($score > 0.3) {
                    $ranked[] = ['score' => $score, 'chunk' => $chunk];
                }
            }

            usort($ranked, fn ($left, $right) => $right['score'] <=> $left['score']);
            $ranked = array_slice($ranked, 0, 10);
        });

        $context = collect($ranked)->map(fn ($item) => sprintf(
            "[File: %s, Hal: %s]\n%s",
            $item['chunk']->document_name,
            $item['chunk']->page_number ?? '-',
            $item['chunk']->chunk_text
        ))->implode("\n\n---\n\n");

        $sources = collect($ranked)->map(fn ($item) => [
            'name' => $item['chunk']->document_name,
            'page' => $item['chunk']->page_number,
        ])->unique(fn ($source) => $source['name'].'|'.$source['page'])->values()->all();

        return [$context === '' ? '' : "Gunakan konteks dokumen RPJMD berikut untuk menjawab:\n\n{$context}", $sources];
    }

    private function cosineSimilarity(array $left, array $right): float
    {
        $dot = $normLeft = $normRight = 0.0;
        foreach ($left as $index => $value) {
            $other = $right[$index] ?? 0;
            $dot += $value * $other;
            $normLeft += $value ** 2;
            $normRight += $other ** 2;
        }

        return $normLeft === 0.0 || $normRight === 0.0
            ? 0.0
            : $dot / (sqrt($normLeft) * sqrt($normRight));
    }

    private function greeting(): string
    {
        $hour = (int) now()->format('H');

        return match (true) {
            $hour >= 11 && $hour < 15 => 'Selamat siang',
            $hour >= 15 && $hour < 18 => 'Selamat sore',
            $hour >= 18 => 'Selamat malam',
            default => 'Selamat pagi',
        };
    }

    private function systemPrompt(string $greeting): string
    {
        return "Anda adalah Asisten Virtual PRivIA Kabupaten Pasuruan.\n\n"
            .'Jawab dalam Bahasa Indonesia yang ramah, ringkas, dan mudah dipahami. '
            .'Gunakan konteks dokumen jika tersedia dan jangan mengarang fakta. '
            .'Jika informasi tidak tersedia dalam konteks, sampaikan dengan jujur. '
            ."Gunakan Markdown sederhana untuk paragraf dan daftar. Sapaan: {$greeting}.";
    }
}
