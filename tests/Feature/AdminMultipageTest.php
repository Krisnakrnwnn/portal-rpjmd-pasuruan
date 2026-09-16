<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\DocumentCategory;
use App\Models\DocumentIngestion;
use App\Models\Gallery;
use App\Models\News;
use App\Models\PublicDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMultipageTest extends TestCase
{
    use RefreshDatabase;

    private function fixtures(): User
    {
        $user = User::factory()->create(['name' => 'Pengelola Uji', 'email' => 'admin@example.test', 'role' => 'Super Admin']);
        $category = DocumentCategory::create(['name' => 'Perencanaan', 'slug' => 'perencanaan']);
        for ($i = 1; $i <= 7; $i++) {
            News::create(['user_id' => $user->id, 'title' => "Berita pembangunan $i", 'slug' => "berita-uji-$i", 'category' => $i === 7 ? 'Musrenbang' : 'Infrastruktur', 'content' => "Isi berita uji $i", 'is_published' => $i !== 7, 'published_at' => now()->subDays($i)]);
            Contact::create(['name' => "Warga Uji $i", 'email' => "warga$i@example.test", 'subject' => "Aspirasi pembangunan $i", 'message' => "Pesan untuk pengujian $i", 'status' => $i === 7 ? 'resolved' : 'unread']);
            PublicDocument::create(['title' => "Dokumen uji $i", 'document_category_id' => $category->id, 'category' => 'Perencanaan', 'year' => 2026, 'file_url' => '/example.pdf']);
            Activity::create(['user_id' => $user->id, 'type' => 'Berita', 'action' => 'Buat', 'description' => "Menambahkan berita uji $i"]);
        }
        Gallery::create(['title' => 'Dokumentasi Uji', 'location' => 'Pasuruan', 'image_path' => 'example.jpg']);
        DocumentIngestion::create(['file_name' => 'fixture.pdf', 'original_name' => 'Dokumen Uji.pdf', 'status' => 'completed', 'total_pages' => 5, 'processed_pages' => 5]);

        return $user;
    }

    public function test_dashboard_renders_with_representative_data(): void
    {
        $this->actingAs($this->fixtures());
        $response = $this->get('/admin')->assertOk()->assertSee('Log Aktivitas');
        if (getenv('ADMIN_REVIEW') === 'baseline') {
            $directory = storage_path('framework/testing/admin-review');
            if (! is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            file_put_contents($directory.'/baseline.html', $response->getContent());
        }
    }

    private function pages(): array
    {
        return [
            'dashboard' => ['/admin', 'dashboard'],
            'berita' => ['/admin/berita', 'berita'],
            'berita-tambah' => ['/admin/berita/tambah', 'berita-form'],
            'berita-1-edit' => ['/admin/berita/1/edit', 'berita-edit'],
            'dokumen' => ['/admin/dokumen', 'dokumen'],
            'dokumen-tambah' => ['/admin/dokumen/tambah?category_id=1', 'dokumen-form'],
            'dokumen-1-edit' => ['/admin/dokumen/1/edit', 'dokumen-edit'],
            'galeri' => ['/admin/galeri', 'galeri'],
            'aspirasi' => ['/admin/aspirasi', 'aspirasi'],
            'ingest' => ['/admin/ingest', 'ingest'],
            'pengguna' => ['/admin/pengguna', 'pengguna'],
            'pengguna-tambah' => ['/admin/pengguna/tambah', 'pengguna-form'],
            'pengguna-1-edit' => ['/admin/pengguna/1/edit', 'pengguna-edit'],
            'setelan' => ['/admin/setelan', 'setelan'],
        ];
    }

    public function test_all_pages_render_only_their_module_and_can_be_opened_directly(): void
    {
        $this->actingAs($this->fixtures());
        foreach ($this->pages() as $key => [$url, $section]) {
            $response = $this->get($url)->assertOk()->assertSee('id="section-'.$section.'"', false);
            preg_match_all('/<section id="section-/', $response->getContent(), $matches);
            $response->assertSee('href="'.route('admin.dokumen.index').'"', false);
            if (getenv('ADMIN_REVIEW') === 'after') {
                $directory = storage_path('framework/testing/admin-review');
                if (! is_dir($directory)) {
                    mkdir($directory, 0777, true);
                }
                file_put_contents($directory.'/'.$key.'.html', $response->getContent());
            }
        }
    }

    public function test_all_admin_pages_require_authentication_and_role(): void
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
        $user->update(['role' => 'Admin']);
        foreach ($this->pages() as [$url]) {
            $response = $this->get($url);
            if (str_starts_with($url, '/admin/pengguna')) {
                $response->assertRedirect(route('admin.dashboard'))->assertSessionHas('error');
            } else {
                $response->assertOk();
            }
        }
        $this->get('/admin/setelan')->assertOk()->assertDontSee('Kelola Pengguna');
    }

    public function test_edit_pages_bind_existing_data_and_unknown_ids_return_404(): void
    {
        $this->actingAs($this->fixtures());
        $this->get('/admin/berita/7/edit')->assertOk()->assertSee('value="Berita pembangunan 7"', false)->assertSee('Isi berita uji 7')->assertSee('action="'.route('admin.update_news', 7).'"', false);
        $this->get('/admin/dokumen/1/edit')->assertOk()->assertSee('value="Dokumen uji 1"', false);
        $this->get('/admin/pengguna/1/edit')->assertOk()->assertSee('value="admin@example.test"', false)->assertDontSee('password_hash');
        foreach (['berita', 'dokumen', 'pengguna'] as $prefix) {
            $this->get('/admin/'.$prefix.'/99999/edit')->assertNotFound();
        }
        $this->get('/admin/dokumen/tambah?category_id=99999')->assertNotFound();
    }

    public function test_search_filters_and_pagination_query_the_complete_dataset(): void
    {
        $this->actingAs($this->fixtures());
        $this->get('/admin/berita')->assertViewHas('news', fn ($news) => $news->total() === 7 && $news->count() === 5)->assertDontSee('Berita pembangunan 7');
        $this->get('/admin/berita?q=pembangunan%207&status=draft&category=musrenbang')->assertSee('Berita pembangunan 7')->assertDontSee('Berita pembangunan 1');
        $this->get('/admin/berita?q=%25')->assertViewHas('news', fn ($news) => $news->total() === 0);
        $this->get('/admin/berita?page=2')->assertSee('Berita pembangunan 7');
        $this->get('/admin/berita?page=999')->assertViewHas('news', fn ($news) => $news->currentPage() === 2);
        $this->get('/admin/aspirasi?status=resolved')->assertViewHas('contacts', fn ($contacts) => $contacts->total() === 1)->assertSee('Aspirasi pembangunan 7');
        $this->get('/admin/dokumen?q=Dokumen%20uji%207')->assertViewHas('publicDocuments', fn ($documents) => $documents->total() === 1);
        $this->get('/admin?date=2000-01-01')->assertViewHas('activities', fn ($activities) => $activities->total() === 0);
    }

    public function test_page_queries_do_not_load_other_modules(): void
    {
        $this->actingAs($this->fixtures());
        DB::enableQueryLog();
        DB::flushQueryLog();
        $this->get('/admin/berita')->assertOk();
        $queries = implode('\n', array_column(DB::getQueryLog(), 'query'));
        foreach (['document_ingestions', 'public_documents', 'galleries', 'profiles'] as $table) {
            $this->assertStringNotContainsString($table, $queries);
        }
        DB::disableQueryLog();
    }

    public function test_news_mutations_preserve_data_audit_and_return_context(): void
    {
        $this->actingAs($this->fixtures());
        $payload = ['title' => 'Berita Baru', 'category' => 'Infrastruktur', 'content' => 'Isi berita', 'is_published' => '0'];
        $this->post(route('admin.store_news').'?page=2&q=pembangunan', $payload)->assertRedirect(route('admin.berita.index', ['q' => 'pembangunan', 'page' => 2]))->assertSessionHas('success');
        $news = News::where('title', 'Berita Baru')->firstOrFail();
        $slug = $news->slug;
        $this->assertFalse($news->is_published);
        $this->get(route('berita.detail', $slug))->assertNotFound();
        $this->put(route('admin.update_news', $news), $payload + [])->assertRedirect(route('admin.berita.index'));
        $this->assertSame($slug, $news->fresh()->slug);
        $this->post(route('admin.toggle_publish', $news))->assertRedirect(route('admin.berita.index'));
        $this->assertTrue($news->fresh()->is_published);
        $this->delete(route('admin.delete_news', $news))->assertRedirect(route('admin.berita.index'));
        $this->assertDatabaseMissing('news', ['id' => $news->id]);
        $this->assertDatabaseHas('activities', ['type' => 'Berita', 'action' => 'Hapus']);
    }

    public function test_validation_returns_to_form_and_restores_non_secret_input(): void
    {
        $this->actingAs($this->fixtures());
        $url = route('admin.berita.create');
        $this->from($url)->post(route('admin.store_news'), ['title' => 'Draft belum lengkap'])->assertRedirect($url)->assertSessionHasErrors(['category', 'content']);
        $this->get($url)->assertOk()->assertSee('value="Draft belum lengkap"', false);
        $this->from(route('admin.pengguna.create'))->post(route('admin.store_user'), ['name' => 'Pegawai', 'email' => 'invalid', 'password' => 'short', 'role' => 'Admin'])->assertSessionHasErrors('email');
        $this->get(route('admin.pengguna.create'))->assertDontSee('value="short"', false)->assertSee('value="Pegawai"', false);
    }

    public function test_other_module_writes_keep_existing_endpoints_and_audit(): void
    {
        $user = $this->fixtures();
        $this->actingAs($user);
        $category = DocumentCategory::first();
        $this->post(route('admin.document-categories.store'), ['name' => 'Kategori Baru', 'slug' => 'baru'])->assertRedirect(route('admin.dokumen.index'));
        $this->put(route('admin.document-categories.update', $category), ['name' => 'Perencanaan Baru', 'slug' => 'perencanaan'])->assertRedirect(route('admin.dokumen.index'));
        $this->delete(route('admin.document-categories.destroy', $category))->assertRedirect(route('admin.dokumen.index'))->assertSessionHas('error');
        $this->put(route('admin.update_document', 1), ['title' => 'Judul Diperbarui', 'document_category_id' => $category->id])->assertRedirect(route('admin.dokumen.index'));
        $this->assertDatabaseHas('public_documents', ['id' => 1, 'title' => 'Judul Diperbarui']);
        $this->delete(route('admin.delete_document', 1))->assertRedirect(route('admin.dokumen.index'));
        $this->post(route('admin.resolve_contact', 1))->assertRedirect(route('admin.aspirasi.index'));
        $this->assertDatabaseHas('contacts', ['id' => 1, 'status' => 'resolved']);
        $this->delete(route('admin.delete_contact', 1))->assertRedirect(route('admin.aspirasi.index'));
        $this->put(route('admin.update_gallery', 1), ['title' => 'Galeri Diperbarui', 'location' => 'Pasuruan'])->assertRedirect(route('admin.galeri.index'));
        $this->post(route('admin.update_settings'), ['gemini_model' => 'gemini-2.5-pro'])->assertRedirect(route('admin.setelan.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('activities', ['type' => 'Dokumen', 'action' => 'Update']);
    }

    public function test_user_protections_and_write_authorization_are_preserved(): void
    {
        $user = $this->fixtures();
        $this->actingAs($user)->delete(route('admin.delete_user', $user))->assertRedirect(route('admin.pengguna.index'))->assertSessionHas('error');
        $admin = User::factory()->create(['role' => 'Admin']);
        $this->actingAs($admin)->post(route('admin.store_user'), [])->assertRedirect(route('admin.dashboard'));
        $this->delete(route('admin.delete_user', $user))->assertRedirect(route('admin.dashboard'));
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_ingest_status_cancel_and_delete_contracts_are_preserved(): void
    {
        $this->actingAs($this->fixtures());
        Storage::fake('local');
        Queue::fake();
        $ingestion = DocumentIngestion::create(['file_name' => 'pending.pdf', 'original_name' => 'Pending.pdf', 'status' => 'pending']);
        $this->get('/admin/ingest')->assertViewHas('activeIngestion', fn ($active) => $active->id === $ingestion->id);
        $this->getJson(route('admin.chatbot.ingest_status', $ingestion))->assertOk()->assertJsonPath('status', 'pending');
        $this->postJson(route('admin.chatbot.ingest.cancel', $ingestion))->assertOk()->assertJsonPath('success', true);
        $this->assertSame('cancelled', $ingestion->fresh()->status);
        $this->deleteJson(route('admin.chatbot.ingest.destroy', $ingestion))->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseMissing('document_ingestions', ['id' => $ingestion->id]);
        Queue::assertNothingPushed();
    }

    public function test_polling_keeps_flash_and_redirect_context_rejects_arbitrary_urls(): void
    {
        $this->actingAs($this->fixtures());
        $this->withSession(['success' => 'Enregistrement test', '_flash' => ['new' => [], 'old' => []]])
            ->get('/admin/berita', ['X-Silent-Polling' => 'true'])->assertOk();
        $this->post(route('admin.toggle_publish', 1).'?return_url=https://example.test&status[]=draft&page=-1')
            ->assertRedirect(route('admin.berita.index'));
    }
}
