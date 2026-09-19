<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\DocumentCategory;
use App\Models\DocumentIngestion;
use App\Models\PublicDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMultipageTest extends TestCase
{
    use RefreshDatabase;

    private function fixtures(): User
    {
        $user = User::factory()->create(['name' => 'Pengelola Uji', 'email' => 'admin@example.test', 'role' => 'Super Admin']);
        $category = DocumentCategory::create(['name' => 'Perencanaan', 'slug' => 'perencanaan']);
        PublicDocument::create(['title' => 'Dokumen uji 1', 'document_category_id' => $category->id, 'category' => 'Perencanaan', 'year' => 2026, 'file_url' => '/example.pdf']);
        Activity::create(['user_id' => $user->id, 'type' => 'Dokumen', 'action' => 'Buat', 'description' => 'Menambahkan dokumen uji']);
        DocumentIngestion::create(['file_name' => 'fixture.pdf', 'original_name' => 'Dokumen Uji.pdf', 'status' => 'completed', 'total_pages' => 5, 'processed_pages' => 5]);

        return $user;
    }

    private function pages(): array
    {
        return [
            'dashboard' => ['/admin', 'dashboard'],
            'dokumen' => ['/admin/dokumen', 'dokumen'],
            'dokumen-tambah' => ['/admin/dokumen/tambah?category_id=1', 'dokumen-form'],
            'dokumen-1-edit' => ['/admin/dokumen/1/edit', 'dokumen-edit'],
            'ingest' => ['/admin/ingest', 'ingest'],
            'pengguna' => ['/admin/pengguna', 'pengguna'],
            'pengguna-tambah' => ['/admin/pengguna/tambah', 'pengguna-form'],
            'pengguna-1-edit' => ['/admin/pengguna/1/edit', 'pengguna-edit'],
            'setelan' => ['/admin/setelan', 'setelan'],
        ];
    }

    public function test_dashboard_and_remaining_admin_pages_render(): void
    {
        $this->actingAs($this->fixtures());

        foreach ($this->pages() as [$url, $section]) {
            $this->get($url)->assertOk()->assertSee('id="section-'.$section.'"', false);
        }
    }

    public function test_removed_content_routes_are_not_registered(): void
    {
        foreach (['admin.berita.index', 'admin.berita.create', 'admin.berita.edit', 'admin.galeri.index', 'admin.aspirasi.index', 'admin.store_news', 'admin.store_gallery', 'admin.resolve_contact'] as $routeName) {
            $this->assertFalse(Route::has($routeName));
        }
    }

    public function test_remaining_admin_pages_require_authentication_and_role(): void
    {
        $user = $this->fixtures();
        foreach ($this->pages() as [$url]) {
            $this->get($url)->assertRedirect(route('login'));
        }

        $user->update(['role' => 'User']);
        $this->actingAs($user);
        foreach ($this->pages() as [$url]) {
            $this->get($url)->assertRedirect('/');
        }
    }

    public function test_document_and_ingest_contracts_remain_available(): void
    {
        $user = $this->fixtures();
        $this->actingAs($user);
        $category = DocumentCategory::first();

        $this->put(route('admin.update_document', 1), ['title' => 'Judul Diperbarui', 'document_category_id' => $category->id])
            ->assertRedirect(route('admin.dokumen.index'));
        $this->assertDatabaseHas('public_documents', ['id' => 1, 'title' => 'Judul Diperbarui']);

        Storage::fake('local');
        Queue::fake();
        $ingestion = DocumentIngestion::create(['file_name' => 'pending.pdf', 'original_name' => 'Pending.pdf', 'status' => 'pending']);
        $this->get('/admin/ingest')->assertViewHas('activeIngestion', fn ($active) => $active->id === $ingestion->id);
        $this->postJson(route('admin.chatbot.ingest.cancel', $ingestion))->assertOk()->assertJsonPath('success', true);
        $this->assertSame('cancelled', $ingestion->fresh()->status);
    }
}
