<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\DocumentChunk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PriviaConversationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.gemini.api_key' => 'fake-privia-key',
            'ai.default_provider' => 'gemini',
            'ai.default_model' => 'gemini-2.5-flash',
        ]);
    }

    public function test_guest_is_redirected_and_authenticated_user_can_open_privia(): void
    {
        $this->get('/privia')->assertRedirect('/login');

        $this->actingAs(User::factory()->create(['name' => 'Krisna']))
            ->get('/privia')
            ->assertOk()
            ->assertSee('PRivIA')
            ->assertSeeHtml('data-user-name="Krisna"')
            ->assertSeeHtml('id="privia-dialog"')
            ->assertSee('Simpan Perubahan')
            ->assertSee('privia-dialog__button--primary');
    }

    public function test_first_and_follow_up_messages_stay_in_one_conversation(): void
    {
        $user = User::factory()->create();
        $this->fakeAi();

        $first = $this->actingAs($user)->postJson('/privia/messages', ['message' => 'Apa itu RPJMD?']);
        $first->assertOk()->assertJsonPath('assistant_message.content', 'Jawaban uji.');
        $conversationId = $first->json('conversation.id');

        $this->actingAs($user)->postJson('/privia/messages', [
            'message' => 'Jelaskan lebih singkat.',
            'conversation_id' => $conversationId,
        ])->assertOk();

        $this->assertSame(1, Conversation::where('user_id', $user->id)->count());
        $this->assertSame(4, ConversationMessage::where('conversation_id', $conversationId)->count());
    }

    public function test_authenticated_popup_uses_shared_persistence_endpoint_and_expand_target(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertOk()
            ->assertSee('/privia/messages')
            ->assertSee('expand-chat')
            ->assertSee('/privia/c/');
    }

    public function test_conversation_operations_are_owner_scoped(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $conversation = Conversation::create(['user_id' => $owner->id, 'title' => 'Percakapan pemilik']);

        $this->actingAs($other)->getJson("/privia/conversations/{$conversation->id}")->assertNotFound();
        $this->actingAs($other)->patchJson("/privia/conversations/{$conversation->id}", ['title' => 'Tidak boleh'])->assertNotFound();
        $this->actingAs($other)->deleteJson("/privia/conversations/{$conversation->id}")->assertNotFound();

        $this->actingAs($owner)->patchJson("/privia/conversations/{$conversation->id}", ['title' => 'Percakapan baru'])
            ->assertOk()
            ->assertJsonPath('conversation.title', 'Percakapan baru');
        $this->actingAs($owner)->deleteJson("/privia/conversations/{$conversation->id}")->assertOk();
        $this->assertDatabaseMissing('conversations', ['id' => $conversation->id]);
    }

    public function test_ai_failure_keeps_user_message_without_fake_assistant(): void
    {
        $user = User::factory()->create();
        Http::fake([
            '*:embedContent*' => Http::response(['embedding' => ['values' => [1, 0]]]),
            '*:generateContent*' => Http::response(['error' => ['message' => 'provider unavailable']], 400),
        ]);

        $response = $this->actingAs($user)->postJson('/privia/messages', ['message' => 'Pertanyaan gagal']);

        $response->assertStatus(502)->assertJsonPath('message', 'Model tidak tersedia atau akses layanan ditolak. Hubungi pengelola server.');
        $this->assertDatabaseCount('conversation_messages', 1);
        $this->assertDatabaseHas('conversation_messages', ['role' => 'user', 'content' => 'Pertanyaan gagal']);
    }

    public function test_valid_retrieved_source_is_returned_without_inventing_sources(): void
    {
        $user = User::factory()->create();
        DocumentChunk::create([
            'document_name' => 'RPJMD Kabupaten Pasuruan 2025-2029.pdf',
            'page_number' => 8,
            'chunk_text' => 'Visi pembangunan Kabupaten Pasuruan.',
            'embedding' => [1, 0],
        ]);
        $this->fakeAi();

        $response = $this->actingAs($user)->postJson('/privia/messages', ['message' => 'Apa visi pembangunan?']);

        $response->assertOk()->assertJsonPath('assistant_message.metadata.sources.0.name', 'RPJMD Kabupaten Pasuruan 2025-2029.pdf');
        $response->assertJsonPath('assistant_message.metadata.sources.0.page', 8);
    }

    private function fakeAi(): void
    {
        Http::fake([
            '*:embedContent*' => Http::response(['embedding' => ['values' => [1, 0]]]),
            '*:generateContent*' => Http::response([
                'candidates' => [['content' => ['parts' => [['text' => 'Jawaban uji.']]]]],
            ]),
        ]);
    }
}
