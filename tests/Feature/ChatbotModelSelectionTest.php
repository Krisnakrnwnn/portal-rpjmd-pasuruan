<?php

namespace Tests\Feature;

use App\Http\Controllers\ChatbotController;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\DocumentChunk;
use App\Models\News;
use App\Models\Stat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ChatbotModelSelectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gemini.api_key' => 'fake-ai-test-key']);
        Http::preventStrayRequests();
        Sleep::fake();
        $this->travelTo(now()->setDate(2026, 9, 15)->setTime(9, 0));
    }

    private function fakeGeneration(): void
    {
        Http::fake([
            '*:embedContent*' => Http::response(['embedding' => ['values' => [1, 0]]]),
            '*:generateContent*' => Http::response(['candidates' => [['content' => ['parts' => [['text' => 'Jawaban uji.']]]]]]),
        ]);
    }

    public function test_existing_rag_news_history_and_persistence_payload(): void
    {
        $this->fakeGeneration();
        for ($i = 0; $i < 11; $i++) {
            DocumentChunk::create(['document_name' => "RPJMD-$i.pdf", 'page_number' => $i + 1, 'chunk_text' => "Konteks $i", 'embedding' => [1, $i / 10]]);
        }
        foreach (range(1, 4) as $i) {
            News::create(['title' => "Berita $i", 'slug' => "berita-$i", 'content' => 'Isi', 'category' => 'Uji', 'is_published' => true, 'published_at' => now(), 'created_at' => now()->subDays($i)]);
        }
        News::create(['title' => 'Draf rahasia', 'slug' => 'draf', 'content' => 'Isi', 'category' => 'Uji', 'is_published' => false]);
        $history = [];
        foreach (range(1, 4) as $i) {
            $history[] = ['role' => 'user', 'parts' => [['text' => "Tanya $i"]]];
            $history[] = ['role' => 'model', 'parts' => [['text' => "Jawab $i"]]];
        }
        $response = $this->withSession(['chatbot_history' => $history])->postJson('/api/chat', ['message' => 'Prioritas?', 'language' => 'id']);
        $response->assertOk()->assertExactJson(['reply' => 'Jawaban uji.'])->assertCookie('chat_session_id');
        Http::assertSent(function (Request $request) use ($history) {
            if (! str_contains($request->url(), ':generateContent')) {
                return false;
            }
            $this->assertStringContainsString('/v1beta/models/gemini-2.5-flash:', $request->url());
            $this->assertSame($history, array_slice($request['contents'], 0, 8));
            $prompt = $request['contents'][8]['parts'][0]['text'];
            $this->assertStringContainsString('Gunakan sapaan: \'Selamat pagi\'', $prompt);
            $this->assertSame(10, substr_count($prompt, '[File:'));
            $this->assertStringContainsString('[File: RPJMD-0.pdf, Hal: 1]', $prompt);
            $this->assertStringNotContainsString('RPJMD-10.pdf', $prompt);
            $this->assertStringContainsString('3. Berita 3 (Rilis: 15 Sep 2026)', $prompt);
            $this->assertStringNotContainsString('Berita 4', $prompt);
            $this->assertStringNotContainsString('Draf rahasia', $prompt);
            $this->assertStringEndsWith('Pertanyaan Baru Warga: Prioritas?', $prompt);

            return true;
        });
        Http::assertSent(fn (Request $request) => str_contains($request->url(), '/gemini-embedding-001:embedContent') && $request['model'] === 'models/gemini-embedding-001' && $request['content']['parts'][0]['text'] === 'Prioritas?');
        $this->assertDatabaseCount('chat_sessions', 1);
        $this->assertDatabaseCount('chat_messages', 2);
        $this->assertSame('Prioritas?', ChatMessage::where('role', 'user')->first()->message);
        $this->assertSame(array_merge(array_slice($history, 2), [
            ['role' => 'user', 'parts' => [['text' => 'Prioritas?']]],
            ['role' => 'model', 'parts' => [['text' => 'Jawaban uji.']]],
        ]), session('chatbot_history'));
        $this->assertDatabaseCount('document_chunks', 11);
    }

    public function test_empty_corpus_still_embeds_and_database_model_is_used(): void
    {
        $this->fakeGeneration();
        Stat::where('key', 'gemini_model')->update(['value' => 'gemini-2.5-pro']);
        $this->postJson('/api/chat', ['message' => 'Hello', 'language' => 'en'])->assertOk();
        Http::assertSentCount(2);
        Http::assertSent(function (Request $request) {
            if (! str_contains($request->url(), '/gemini-2.5-pro:generateContent')) {
                return false;
            }
            $prompt = $request['contents'][0]['parts'][0]['text'];
            $this->assertStringContainsString('ALWAYS answer in English', $prompt);
            $this->assertStringNotContainsString('Gunakan konteks dokumen', $prompt);

            return true;
        });
    }

    public function test_low_relevance_does_not_inject_context(): void
    {
        $this->fakeGeneration();
        DocumentChunk::create(['document_name' => 'Tidak relevan.pdf', 'chunk_text' => 'Tidak dipakai', 'embedding' => [0, 1]]);
        $this->postJson('/api/chat', ['message' => 'Prioritas?'])->assertOk();
        Http::assertSent(fn (Request $request) => str_contains($request->url(), ':generateContent') && ! str_contains($request['contents'][0]['parts'][0]['text'], 'Tidak dipakai'));
    }

    public function test_next_request_uses_saved_model_without_restarting_services(): void
    {
        $this->fakeGeneration();
        $this->postJson('/api/chat', ['message' => 'Pertanyaan pertama'])->assertOk();
        $this->actingAs(User::factory()->create(['role' => 'Admin']))
            ->post('/admin/settings', ['provider' => 'gemini', 'gemini_model' => 'gemini-2.5-pro'])->assertSessionHas('success');
        $this->postJson('/api/chat', ['message' => 'Pertanyaan kedua'])->assertOk();
        $calls = Http::recorded(fn (Request $r) => str_contains($r->url(), ':generateContent'))->values();
        $this->assertCount(2, $calls);
        $this->assertStringContainsString('/gemini-2.5-flash:', $calls[0][0]->url());
        $this->assertStringContainsString('/gemini-2.5-pro:', $calls[1][0]->url());
        $this->assertSame('Pertanyaan pertama', $calls[1][0]['contents'][0]['parts'][0]['text']);
        $this->assertDatabaseCount('chat_messages', 4);
    }

    public function test_default_generation_and_invalid_database_value_are_safe(): void
    {
        $this->fakeGeneration();
        Stat::where('key', 'gemini_model')->delete();
        $this->postJson('/api/chat', ['message' => 'Default'])->assertOk();
        Stat::create(['key' => 'gemini_model', 'value' => '../invalid?key=secret']);
        $this->postJson('/api/chat', ['message' => 'Fallback'])->assertOk();
        foreach (Http::recorded(fn (Request $r) => str_contains($r->url(), ':generateContent')) as [$request]) {
            $this->assertStringContainsString('/gemini-2.5-flash:', $request->url());
            $this->assertStringNotContainsString('secret', $request->url());
        }
        $this->assertDatabaseHas('stats', ['key' => 'gemini_model', 'value' => '../invalid?key=secret']);
    }

    public static function publicErrors(): array
    {
        return [[400, 500], [401, 500], [403, 500], [404, 200], [429, 200], [503, 200]];
    }

    #[DataProvider('publicErrors')]
    public function test_public_error_status_contract_and_safe_reply(int $providerStatus, int $publicStatus): void
    {
        Http::fake([
            '*:embedContent' => Http::response(['embedding' => ['values' => [1, 0]]]),
            '*:generateContent' => Http::response(['error' => ['message' => 'raw fake-ai-test-key']], $providerStatus),
        ]);
        $this->postJson('/api/chat', ['message' => 'Pertanyaan'])->assertStatus($publicStatus)->assertJsonStructure(['reply'])->assertDontSee('fake-ai-test-key')->assertDontSee('raw');
        $this->assertDatabaseCount('chat_messages', 1);
        $this->assertNull(session('chatbot_history'));
        $this->assertDatabaseHas('stats', ['key' => 'gemini_model', 'value' => 'gemini-2.5-flash']);
    }

    public function test_missing_key_and_embedding_exception_do_not_leak_details(): void
    {
        config(['services.gemini.api_key' => null]);
        $this->postJson('/api/chat', ['message' => 'Tanya'])->assertStatus(500);
        Http::assertNothingSent();
        $this->assertDatabaseCount('chat_messages', 0);
        config(['services.gemini.api_key' => 'fake-ai-test-key']);
        Http::fake(fn () => throw new ConnectionException('URL?key=fake-ai-test-key'));
        $this->postJson('/api/chat', ['message' => 'Tanya'])->assertStatus(500)->assertDontSee('fake-ai-test-key')->assertDontSee('URL');
    }

    public function test_original_system_prompt_snapshots_are_unchanged(): void
    {
        // Hashes recorded from the pre-refactor source; normalize platform newlines only.
        $method = new \ReflectionMethod(ChatbotController::class, 'getSystemPrompt');
        foreach (['en' => '2bf2df6128a32be0f11052fc1d62573e425e0dc70cb00f4f0238b595e168f7c2', 'id' => 'd9a2b87ab0fab037d1987466ba4589130cb7b9c418d688827a421aab0dc0e82a'] as $language => $hash) {
            $prompt = $method->invoke(app(ChatbotController::class), $language, 'Selamat pagi');
            $this->assertSame($hash, hash('sha256', str_replace("\r\n", "\n", $prompt)));
        }
    }

    public static function relevanceBoundaries(): array
    {
        return [[0.29, false], [0.3, false], [0.31, true]];
    }

    #[DataProvider('relevanceBoundaries')]
    public function test_retrieval_threshold_and_inclusion_of_lower_ranked_chunks(float $score, bool $included): void
    {
        $this->fakeGeneration();
        DocumentChunk::create(['document_name' => 'Utama.pdf', 'chunk_text' => 'Konteks utama', 'embedding' => [$score, sqrt(1 - $score ** 2)]]);
        DocumentChunk::create(['document_name' => 'Rendah.pdf', 'chunk_text' => 'Konteks rendah', 'embedding' => [0, 1]]);
        $this->postJson('/api/chat', ['message' => 'Prioritas?'])->assertOk();
        $request = Http::recorded(fn (Request $r) => str_contains($r->url(), ':generateContent'))->first()[0];
        $prompt = $request['contents'][0]['parts'][0]['text'];
        $this->assertSame($included, str_contains($prompt, 'Konteks utama'));
        $this->assertSame($included, str_contains($prompt, 'Konteks rendah'));
    }

    public static function embeddingErrors(): array
    {
        return [[429, 3], [503, 1], [400, 1]];
    }

    #[DataProvider('embeddingErrors')]
    public function test_embedding_failure_preserves_retry_contract(int $status, int $attempts): void
    {
        Http::fake(['*:embedContent' => Http::response(['error' => 'private provider detail'], $status)]);
        $this->postJson('/api/chat', ['message' => 'Pertanyaan'])->assertStatus(500)->assertDontSee('private provider detail');
        Http::assertSentCount($attempts);
        Sleep::assertSleptTimes($status === 429 ? 3 : 0);
        $this->assertDatabaseCount('chat_messages', 1);
        $this->assertNull(session('chatbot_history'));
    }

    public function test_invalid_generation_configuration_returns_actionable_safe_message(): void
    {
        $this->fakeGeneration();
        Stat::where('key', 'gemini_model')->delete();
        config(['ai.default_model' => 'invalid']);
        $this->postJson('/api/chat', ['message' => 'Pertanyaan'])->assertStatus(500)
            ->assertJsonPath('reply', 'Konfigurasi layanan AI belum siap. Hubungi pengelola server.');
        Http::assertSentCount(1); // Only the unchanged embedding flow runs before resolution.
    }

    public function test_model_save_preserves_history_clear_and_new_session_contract(): void
    {
        $session = ChatSession::create(['session_id' => 'ai-history-test']);
        $other = ChatSession::create(['session_id' => 'ai-other-test']);
        foreach (range(1, 51) as $index) {
            ChatMessage::forceCreate(['session_id' => $session->session_id, 'role' => 'user', 'message' => "Pesan $index", 'created_at' => now()->addSeconds($index)]);
        }
        ChatMessage::create(['session_id' => $other->session_id, 'role' => 'user', 'message' => 'Sesi lain']);
        $history = [['role' => 'user', 'parts' => [['text' => 'Memori']]]];
        $this->actingAs(User::factory()->create(['role' => 'Admin']))->withSession(['chatbot_history' => $history]);
        $this->post('/admin/settings', ['gemini_model' => 'gemini-2.5-pro'])->assertSessionHas('success');
        $this->assertSame($history, session('chatbot_history'));
        $this->withCookie('chat_session_id', $session->session_id)->get('/api/chat/history')
            ->assertOk()->assertJsonCount(50, 'messages')->assertJsonPath('messages.0.text', 'Pesan 1')
            ->assertJsonPath('messages.49.text', 'Pesan 50')->assertDontSee('Sesi lain');
        $this->postJson('/api/chat/clear')->assertOk()->assertCookieMissing('chat_session_id');
        $this->assertNull(session('chatbot_history'));
        $this->assertDatabaseCount('chat_messages', 52);
        $this->postJson('/api/chat/new-session')->assertOk()->assertCookie('chat_session_id');
        $this->assertDatabaseCount('chat_sessions', 3);
        Http::assertNothingSent();
    }
}
