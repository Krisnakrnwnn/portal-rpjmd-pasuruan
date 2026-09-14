<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\DocumentCategory;
use App\Models\DocumentIngestion;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Profile;
use App\Models\PublicDocument;
use App\Models\Service;
use App\Models\Stat;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

/** Read-only page composition. Existing write endpoints retain their contracts. */
class AdminPageController extends Controller
{
    private function filters(Request $request): array
    {
        return $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:publik,draft,all,unread,resolved'],
            'category' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'min:1'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'page' => ['nullable', 'integer', 'min:1'],
            'categories_page' => ['nullable', 'integer', 'min:1'],
            'edit' => ['nullable', 'integer', 'min:1'],
        ]);
    }

    private function search(Builder $query, array $columns, ?string $value): Builder
    {
        $value = mb_strtolower(trim($value ?? ''));
        if ($value !== '') {
            // A bound pattern preserves literal %, _ and backslash searches on SQLite/MySQL.
            $pattern = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $value).'%';
            $query->where(function (Builder $nested) use ($columns, $pattern) {
                foreach ($columns as $column) {
                    $nested->orWhereRaw("LOWER($column) LIKE ? ESCAPE '!'", [$pattern]);
                }
            });
        }

        return $query;
    }

    private function paginate(Builder $query, int $perPage = 5, string $name = 'page'): LengthAwarePaginator
    {
        $page = $query->paginate($perPage, ['*'], $name)->withQueryString();
        if ($page->currentPage() > $page->lastPage()) {
            $page = $query->paginate($perPage, ['*'], $name, $page->lastPage())->withQueryString();
        }

        return $page;
    }

    private function page(string $view, string $module, string $title, array $data = []): View
    {
        $this->filters(request());
        if (request()->header('X-Silent-Polling') === 'true') {
            request()->session()->reflash();
        }
        return view('admin.'.$view, array_merge([
            'adminModule' => $module,
            'pageTitle' => $title,
            'formDefaults' => [],
            'unreadCount' => Contact::where('status', 'unread')->count(),
            'latestContacts' => Contact::where('status', 'unread')->latest()->limit(5)->get(),
        ], $data));
    }

    public function dashboard(Request $request): View
    {
        $filters = $this->filters($request);
        $activities = Activity::with('user')->latest();
        if (! empty($filters['date'])) {
            $start = \Carbon\Carbon::parse($filters['date'], 'Asia/Jakarta')->startOfDay()->setTimezone(config('app.timezone'));
            $activities->where('created_at', '>=', $start)->where('created_at', '<', $start->copy()->addDay());
        }
        $this->search($activities, ['description', 'type', 'action'], $filters['q'] ?? null);

        return $this->page('dashboard', 'dashboard', 'Dashboard Utama', [
            'counts' => [
                'documents' => PublicDocument::count(),
                'ingest_chunks' => \App\Models\DocumentChunk::count(),
                'users' => User::count(),
            ],
            'activities' => $this->paginate($activities, 20),
        ]);
    }

    public function berita(Request $request): View
    {
        $filters = $this->filters($request);
        $query = $this->search(News::with('author')->orderByDesc('published_at'), ['title', 'category'], $filters['q'] ?? null);
        if (in_array($filters['status'] ?? '', ['publik', 'draft'], true)) {
            $query->where('is_published', $filters['status'] === 'publik');
        }
        if (! empty($filters['category'])) {
            $query->whereRaw('LOWER(category) = ?', [mb_strtolower($filters['category'])]);
        }

        return $this->page('berita/index', 'berita', 'Manajemen Berita', [
            'news' => $this->paginate($query),
            'newsCategories' => News::select('category')->distinct()->whereNotNull('category')->cursor()->pluck('category')->filter()->values(),
        ]);
    }

    public function createBerita(): View
    {
        return $this->page('berita/create', 'berita', 'Tambah Berita');
    }

    public function editBerita(News $news): View
    {
        return $this->page('berita/edit', 'berita', 'Edit Berita', ['record' => $news, 'formDefaults' => $news->only(['title', 'category', 'content', 'is_published'])]);
    }

    private function categories()
    {
        return DocumentCategory::with('parent')->orderBy('id')->lazy(200);
    }

    public function dokumen(Request $request): View
    {
        $filters = $this->filters($request);
        $query = PublicDocument::with('documentCategory')->orderByDesc('year')->latest();
        $this->search($query, ['title', 'category'], $filters['q'] ?? null);
        if (! empty($filters['category_id'])) {
            $query->where('document_category_id', $filters['category_id']);
        }

        return $this->page('dokumen/index', 'dokumen', 'Bank Data / Dokumen', [
            'publicDocuments' => $this->paginate($query),
            'documentCategories' => $this->paginate(DocumentCategory::with('parent')->withCount('documents')->orderBy('id'), 20, 'categories_page'),
            'categoryOptions' => $this->categories(),
        ]);
    }

    public function createDokumen(Request $request): View
    {
        $filters = $this->filters($request);
        if (! empty($filters['category_id'])) {
            DocumentCategory::findOrFail($filters['category_id']);
        }

        return $this->page('dokumen/create', 'dokumen', 'Tambah Dokumen', ['documentCategories' => $this->categories(), 'formDefaults' => ['document_category_id' => $filters['category_id'] ?? null]]);
    }

    public function editDokumen(PublicDocument $document): View
    {
        return $this->page('dokumen/edit', 'dokumen', 'Edit Dokumen', ['record' => $document, 'documentCategories' => $this->categories(), 'formDefaults' => $document->only(['title', 'document_category_id'])]);
    }

    public function galeri(Request $request): View
    {
        $filters = $this->filters($request);
        $record = ! empty($filters['edit']) ? Gallery::findOrFail($filters['edit']) : null;

        return $this->page('galeri/index', 'galeri', 'Manajemen Galeri', ['galleries' => $this->paginate($this->search(Gallery::latest(), ['title', 'location'], $filters['q'] ?? null)), 'record' => $record, 'formDefaults' => $record?->only(['title', 'location']) ?? []]);
    }

    public function aspirasi(Request $request): View
    {
        $filters = $this->filters($request);
        $query = $this->search(Contact::latest(), ['name', 'email', 'subject', 'message'], $filters['q'] ?? null);
        if (in_array($filters['status'] ?? '', ['unread', 'resolved'], true)) {
            $query->where('status', $filters['status']);
        }

        return $this->page('aspirasi/index', 'aspirasi', 'Aspirasi & Pesan', ['contacts' => $this->paginate($query)]);
    }

    public function pengguna(Request $request): View
    {
        $filters = $this->filters($request);

        return $this->page('pengguna/index', 'pengguna', 'Kelola Pengguna', ['users' => $this->paginate($this->search(User::orderBy('id'), ['name', 'email', 'role'], $filters['q'] ?? null))]);
    }

    public function createPengguna(): View
    {
        return $this->page('pengguna/create', 'pengguna', 'Tambah Pegawai');
    }

    public function editPengguna(User $user): View
    {
        return $this->page('pengguna/edit', 'pengguna', 'Edit Pegawai', ['record' => $user, 'formDefaults' => $user->only(['name', 'email', 'role'])]);
    }

    public function setelan(): View
    {
        return $this->page('setelan/index', 'setelan', 'Setelan Konfigurasi', ['profiles' => Profile::orderBy('id')->lazy(200)->collect(), 'activeModel' => Stat::where('key', 'gemini_model')->value('value') ?? 'gemini-2.5-flash']);
    }

    public function ingest(Request $request): View
    {
        $filters = $this->filters($request);

        return $this->page('ingest/index', 'ingest', 'Ingest Data Chatbot', [
            'ingestions' => $this->paginate($this->search(DocumentIngestion::latest(), ['original_name', 'status'], $filters['q'] ?? null), 10),
            'activeIngestion' => DocumentIngestion::whereIn('status', ['pending', 'processing'])->latest()->first(),
        ]);
    }
}
