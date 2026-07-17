<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\News;
use App\Models\Activity;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\DocumentIngestion;
use App\Jobs\IngestDocumentJob;

class AdminController extends Controller
{
    public function dashboard()
    {
        $news = News::with('author')->orderBy('published_at', 'desc')->get();
        $contacts = \App\Models\Contact::orderBy('created_at', 'desc')->get();
        $services = \App\Models\Service::all();
        $profiles = \App\Models\Profile::all();
        $users = \App\Models\User::all();
        $sectors = \App\Models\Sector::with('indicators')->get();
        $publicDocuments = \App\Models\PublicDocument::orderBy('year', 'desc')->orderBy('created_at', 'desc')->get();
        $documentCategories = \App\Models\DocumentCategory::all();

        // Pisahkan: Statistik Hero Beranda vs Statistik Capaian RPJMD
        $heroStats = \App\Models\Stat::where('key', 'like', 'hero_%')->get();

        $capaianStats = \App\Models\Stat::where('key', 'not like', 'hero_%')
            ->where('key', '!=', 'gemini_model')
            ->get();

        // Real Statistics for Cards
        $counts = [
            'news' => $news->count(),
            'unread_contacts' => \App\Models\Contact::where('status', 'unread')->count(),
            'services' => $services->count(),
            'users' => $users->count(),
            'sectors' => $sectors->count(),
        ];

        $activities = Activity::with('user')->orderBy('created_at', 'desc')->get();
        $galleries = \App\Models\Gallery::orderBy('created_at', 'desc')->get();

        return view('admin.dashboard', compact('news', 'contacts', 'services', 'heroStats', 'capaianStats', 'profiles', 'users', 'counts', 'activities', 'sectors', 'publicDocuments', 'documentCategories', 'galleries'));
    }

    // --- Kategori Dokumen CRUD ---
    public function storeDocumentCategory(Request $request)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:document_categories,id',
            'name' => 'required|string|max:255|unique:document_categories',
            'slug' => 'required|string|max:255|unique:document_categories',
            'description' => 'nullable|string',
        ]);

        \App\Models\DocumentCategory::create($request->all());

        Activity::log('Kategori', 'Buat', 'Menambahkan kategori dokumen baru: ' . $request->name);

        return redirect(route('admin.dashboard') . '#section-dokumen')->with('success', 'Kategori dokumen berhasil ditambahkan!');
    }

    public function updateDocumentCategory(Request $request, $id)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:document_categories,id',
            'name' => 'required|string|max:255|unique:document_categories,name,' . $id,
            'slug' => 'required|string|max:255|unique:document_categories,slug,' . $id,
            'description' => 'nullable|string',
        ]);

        $category = \App\Models\DocumentCategory::findOrFail($id);
        $category->update($request->all());

        Activity::log('Kategori', 'Update', 'Memperbarui kategori dokumen: ' . $request->name);

        return redirect(route('admin.dashboard') . '#section-dokumen')->with('success', 'Kategori dokumen berhasil diperbarui!');
    }

    public function destroyDocumentCategory($id)
    {
        $category = \App\Models\DocumentCategory::findOrFail($id);
        
        // Cek jika kategori masih dipakai
        if($category->documents()->count() > 0) {
            return redirect(route('admin.dashboard') . '#section-dokumen')->with('error', 'Kategori ini tidak dapat dihapus karena masih digunakan oleh dokumen!');
        }

        $category->delete();

        Activity::log('Kategori', 'Hapus', 'Menghapus kategori dokumen: ' . $category->name);

        return redirect(route('admin.dashboard') . '#section-dokumen')->with('success', 'Kategori dokumen berhasil dihapus!');
    }

    public function storeNews(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'category' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $uploadDir = public_path('uploads/news');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $file->move($uploadDir, $filename);
            $imageUrl = 'uploads/news/' . $filename;
        }

        News::create([
            'user_id'      => auth()->id(),
            'title'        => $request->title,
            'slug'         => Str::slug($request->title) . '-' . uniqid(),
            'category'     => $request->category,
            'content'      => $request->content,
            'image_url'    => $imageUrl,
            'published_at' => now(),
            'is_published' => $request->boolean('is_published', true), // default publik
        ]);

        $status = $request->boolean('is_published', true) ? 'publik' : 'draft';
        Activity::log('Berita', 'Buat', 'Menerbitkan berita baru (' . $status . '): ' . $request->title);

        return redirect(route('admin.dashboard') . '#section-berita')->with('success', 'Berita berhasil disimpan!');
    }

    public function updateNews(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'category' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $news = News::findOrFail($id);
        
        $data = [
            'title'        => $request->title,
            'category'     => $request->category,
            'content'      => $request->content,
            'is_published' => $request->boolean('is_published', true),
        ];

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($news->image_url) {
                $oldImagePath = public_path($news->image_url);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
                // Fallback: hapus dari storage juga jika punya path lama
                if (str_starts_with($news->image_url, 'storage/')) {
                    $oldPath = str_replace('storage/', '', $news->image_url);
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $uploadDir = public_path('uploads/news');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $file->move($uploadDir, $filename);
            $data['image_url'] = 'uploads/news/' . $filename;
        }

        $news->update($data);

        Activity::log('Berita', 'Update', 'Memperbarui berita: ' . $news->title);

        return redirect(route('admin.dashboard') . '#section-berita')->with('success', 'Berita berhasil diperbarui!');
    }

    public function deleteNews($id)
    {
        $news = News::findOrFail($id);

        // Hapus file gambar jika ada
        if ($news->image_url) {
            $oldImagePath = public_path($news->image_url);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
            // Fallback: hapus dari storage juga jika punya path lama
            if (str_starts_with($news->image_url, 'storage/')) {
                $path = str_replace('storage/', '', $news->image_url);
                Storage::disk('public')->delete($path);
            }
        }

        Activity::log('Berita', 'Hapus', 'Menghapus berita: ' . $news->title);
        $news->delete();
        return redirect(route('admin.dashboard') . '#section-berita')->with('success', 'Berita berhasil dihapus!');
    }

    public function togglePublish($id)
    {
        $news = News::findOrFail($id);
        $news->is_published = !$news->is_published;
        $news->save();

        $status = $news->is_published ? 'Dipublikasikan' : 'Dijadikan Draft';
        Activity::log('Berita', 'Toggle', $status . ': ' . $news->title);

        return redirect(route('admin.dashboard') . '#section-berita')
               ->with('success', 'Berita "' . $news->title . '" berhasil ' . strtolower($status) . '!');
    }

    public function storeService(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'url' => 'required',
            'icon' => 'required',
        ]);

        \App\Models\Service::create($request->all());

        Activity::log('Layanan', 'Buat', 'Menambahkan layanan: ' . $request->name);

        return redirect(route('admin.dashboard') . '#section-layanan')->with('success', 'Layanan berhasil ditambahkan!');
    }

    public function updateService(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'url' => 'required',
            'icon' => 'required',
        ]);

        $service = \App\Models\Service::findOrFail($id);
        $service->update($request->all());

        Activity::log('Layanan', 'Update', 'Memperbarui layanan: ' . $service->name);

        return redirect(route('admin.dashboard') . '#section-layanan')->with('success', 'Layanan berhasil diperbarui!');
    }

    public function deleteService($id)
    {
        $service = \App\Models\Service::findOrFail($id);
        Activity::log('Layanan', 'Hapus', 'Menghapus layanan: ' . $service->name);
        $service->delete();
        return redirect(route('admin.dashboard') . '#section-layanan')->with('success', 'Layanan berhasil dihapus!');
    }

    public function storeSector(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'theme_color' => 'required',
            'icon' => 'required'
        ]);
        \App\Models\Sector::create($request->all());
        Activity::log('Capaian', 'Buat', 'Menambah sektor: ' . $request->name);
        return redirect(route('admin.dashboard') . '#section-capaian')->with('success', 'Sektor berhasil ditambahkan!');
    }

    public function updateSector(Request $request, $id)
    {
        $sector = \App\Models\Sector::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'theme_color' => 'required',
            'icon' => 'required'
        ]);
        $sector->update($request->all());
        Activity::log('Capaian', 'Update', 'Memperbarui sektor: ' . $sector->name);
        return redirect(route('admin.dashboard') . '#section-capaian')->with('success', 'Sektor berhasil diperbarui!');
    }

    public function deleteSector($id)
    {
        $sector = \App\Models\Sector::findOrFail($id);
        Activity::log('Capaian', 'Hapus', 'Menghapus sektor: ' . $sector->name);
        $sector->delete();
        return redirect(route('admin.dashboard') . '#section-capaian')->with('success', 'Sektor berhasil dihapus!');
    }

    public function storeIndicator(Request $request)
    {
        $request->validate([
            'sector_id' => 'required|exists:sectors,id',
            'name' => 'required',
            'progress' => 'required|integer|min:0|max:100'
        ]);
        \App\Models\Indicator::create($request->all());
        Activity::log('Capaian', 'Buat', 'Menambah indikator: ' . $request->name);
        return redirect(route('admin.dashboard') . '#section-capaian')->with('success', 'Indikator berhasil ditambahkan!');
    }

    public function updateIndicator(Request $request, $id)
    {
        $indicator = \App\Models\Indicator::findOrFail($id);
        $request->validate([
            'sector_id' => 'required|exists:sectors,id',
            'name' => 'required',
            'progress' => 'required|integer|min:0|max:100'
        ]);
        $indicator->update($request->all());
        Activity::log('Capaian', 'Update', 'Memperbarui indikator: ' . $indicator->name);
        return redirect(route('admin.dashboard') . '#section-capaian')->with('success', 'Indikator berhasil diperbarui!');
    }

    public function deleteIndicator($id)
    {
        $indicator = \App\Models\Indicator::findOrFail($id);
        Activity::log('Capaian', 'Hapus', 'Menghapus indikator: ' . $indicator->name);
        $indicator->delete();
        return redirect(route('admin.dashboard') . '#section-capaian')->with('success', 'Indikator berhasil dihapus!');
    }

    public function updateStats(Request $request)
    {
        foreach ($request->stats as $key => $value) {
            \App\Models\Stat::where('key', $key)->update(['value' => $value]);
        }
        Activity::log('Statistik', 'Update', 'Memperbarui statistik capaian RPJMD.');
        return redirect(route('admin.dashboard') . '#section-dashboard')->with('success', 'Statistik capaian RPJMD berhasil diperbarui!');
    }

    public function updateHeroStats(Request $request)
    {
        foreach ($request->hero_stats as $key => $value) {
            \App\Models\Stat::where('key', $key)->update(['value' => $value]);
        }
        Activity::log('Statistik', 'Update', 'Memperbarui statistik utama halaman beranda.');
        return redirect(route('admin.dashboard') . '#section-dashboard')->with('success', 'Statistik utama beranda berhasil diperbarui!');
    }

    public function storeHeroStat(Request $request)
    {
        $request->validate([
            'label' => 'required|max:40',
            'value' => 'required|max:20',
        ]);

        $key = 'hero_' . preg_replace('/[^a-z0-9]+/', '_', strtolower($request->label));

        // Hindari duplikat key
        $suffix = '';
        $attempt = 0;
        while (\App\Models\Stat::where('key', $key . $suffix)->exists()) {
            $attempt++;
            $suffix = '_' . $attempt;
        }
        $key .= $suffix;

        \App\Models\Stat::create([
            'key'   => $key,
            'label' => $request->label,
            'value' => $request->value,
        ]);

        Activity::log('Statistik', 'Buat', 'Menambah statistik beranda: ' . $request->label);
        return redirect(route('admin.dashboard') . '#section-dashboard')->with('success', 'Statistik beranda "' . $request->label . '" berhasil ditambahkan!');
    }

    public function deleteHeroStat($id)
    {
        $stat = \App\Models\Stat::findOrFail($id);

        // Pastikan hanya hero_ stats yang bisa dihapus dari sini
        if (!str_starts_with($stat->key, 'hero_')) {
            return redirect(route('admin.dashboard') . '#section-dashboard')->with('error', 'Hanya statistik beranda yang dapat dihapus di sini.');
        }

        Activity::log('Statistik', 'Hapus', 'Menghapus statistik beranda: ' . $stat->label);
        $stat->delete();
        return redirect(route('admin.dashboard') . '#section-dashboard')->with('success', 'Statistik "' . $stat->label . '" berhasil dihapus!');
    }

    // public function updateProfile(Request $request)
    // {
    //     $profiles = $request->input('profiles', []);
    //     \Illuminate\Support\Facades\Log::info('updateProfile Request inputs: ', $request->all());
    //     \Illuminate\Support\Facades\Log::info('updateProfile Profiles to update: ', $profiles);
        
    //     foreach ($profiles as $key => $content) {
    //         $updated = \App\Models\Profile::where('key', $key)->update(['content' => $content]);
    //         \Illuminate\Support\Facades\Log::info("Updated key: {$key}, rows affected: {$updated}");
    //     }
    //     Activity::log('Profil', 'Update', 'Memperbarui konten profil instansi.');
    //     return redirect(route('admin.dashboard') . '#section-setelan')->with('success', 'Profil instansi berhasil diperbarui!');
    // }
    public function updateProfile(Request $request)
{
    $profiles = $request->input('profiles', []);
    
    \Illuminate\Support\Facades\Log::info('updateProfile Request inputs: ', $request->all());
    
    if (empty($profiles)) {
        return redirect(route('admin.dashboard') . '#section-setelan')
            ->with('error', 'Tidak ada data profil yang dikirim atau diubah.');
    }
    
    foreach ($profiles as $key => $content) {
        // Membuat judul otomatis (contoh: 'sejarah' menjadi 'Sejarah') jika datanya baru dibuat
        $generatedTitle = ucfirst(str_replace('_', ' ', $key));

        // Menggunakan updateOrCreate untuk menangani update sekaligus insert baru dengan aman
        \App\Models\Profile::updateOrCreate(
            ['key' => $key], // Kondisi pencarian data lama
            [
                'content' => $content,
                'title'   => $generatedTitle // Mengisi kolom title jika data belum pernah ada
            ]
        );
        
        \Illuminate\Support\Facades\Log::info("Berhasil memproses key: {$key}");
    }
    
    Activity::log('Profil', 'Update', 'Memperbarui konten profil instansi.');
    
    return redirect(route('admin.dashboard') . '#section-setelan')
        ->with('success', 'Profil instansi berhasil diperbarui!');
}

    public function resolveContact($id)
    {
        $contact = \App\Models\Contact::findOrFail($id);
        $contact->update(['status' => 'resolved']);
        Activity::log('Aspirasi', 'Selesai', 'Menandai selesai pesan dari: ' . $contact->name);
        return redirect(route('admin.dashboard') . '#section-aspirasi')->with('success', 'Aspirasi dari "' . $contact->name . '" ditandai selesai!');
    }

    public function deleteContact($id)
    {
        $contact = \App\Models\Contact::findOrFail($id);
        $name = $contact->name;
        $contact->delete();
        Activity::log('Aspirasi', 'Hapus', 'Menghapus pesan dari: ' . $name);
        return redirect(route('admin.dashboard') . '#section-aspirasi')->with('success', 'Aspirasi dari "' . $name . '" berhasil dihapus!');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required',
        ]);

        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Hash::make($request->password),
            'role' => $request->role,
        ]);

        Activity::log('Pengguna', 'Buat', 'Mendaftarkan admin baru: ' . $request->name);

        return redirect(route('admin.dashboard') . '#section-pengguna')->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    public function updateUser(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required',
        ]);

        $data = $request->only(['name', 'email', 'role']);
        if ($request->filled('password')) {
            $data['password'] = \Hash::make($request->password);
        }

        $user->update($data);

        Activity::log('Pengguna', 'Update', 'Memperbarui data admin: ' . $user->name);

        return redirect(route('admin.dashboard') . '#section-pengguna')->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function deleteUser($id)
    {
        $user = \App\Models\User::findOrFail($id);

        // Cegah hapus akun sendiri
        if ($user->id === auth()->id()) {
            return redirect(route('admin.dashboard') . '#section-pengguna')
                ->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        // Cegah hapus Super Admin terakhir
        if ($user->role === 'Super Admin') {
            $superAdminCount = \App\Models\User::where('role', 'Super Admin')->count();
            if ($superAdminCount <= 1) {
                return redirect(route('admin.dashboard') . '#section-pengguna')
                    ->with('error', 'Tidak bisa menghapus Super Admin terakhir! Sistem membutuhkan minimal satu Super Admin.');
            }
        }

        Activity::log('Pengguna', 'Hapus', 'Menghapus akun admin: ' . $user->name);
        $user->delete();
        return redirect(route('admin.dashboard') . '#section-pengguna')->with('success', 'Pengguna berhasil dihapus!');
    }

    public function ingestPdf(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:51200', // Tingkatkan ke 50MB
        ]);

        ini_set('memory_limit', '512M');
        set_time_limit(300);

        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . $originalName;
            
            // Store file
            $path = $file->storeAs('documents', $fileName, 'local');

            // Create record
            $ingestion = DocumentIngestion::create([
                'file_name' => $fileName,
                'original_name' => $originalName,
                'status' => 'pending'
            ]);

            // Dispatch job
            IngestDocumentJob::dispatch($ingestion);

            return response()->json([
                'success' => true,
                'message' => 'Proses ingest telah dimulai di background.',
                'ingestion_id' => $ingestion->id
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal mengunggah file.'], 400);
    }

    public function checkIngestStatus($id)
    {
        $ingestion = DocumentIngestion::findOrFail($id);
        
        $estimatedTime = 0;
        if ($ingestion->status === 'processing' && $ingestion->processed_pages > 0) {
            $elapsedSeconds = now()->diffInSeconds($ingestion->started_at);
            $secondsPerPage = $elapsedSeconds / $ingestion->processed_pages;
            $remainingPages = $ingestion->total_pages - $ingestion->processed_pages;
            $estimatedTime = round($secondsPerPage * $remainingPages);
        }

        return response()->json([
            'status' => $ingestion->status,
            'progress' => $ingestion->progress_percentage,
            'processed' => $ingestion->processed_pages,
            'total' => $ingestion->total_pages,
            'estimated_seconds' => $estimatedTime,
            'error' => $ingestion->error_message
        ]);
    }

    public function storeDocument(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'document_category_id' => 'required|exists:document_categories,id',
            'files' => 'required|array',
            'files.*' => 'file|mimes:pdf|max:20480', // 20MB Max per file
        ]);

        $cat = \App\Models\DocumentCategory::find($request->document_category_id);
        $categoryName = $cat ? $cat->name : '-';
        $uploadedCount = 0;

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $uploadDir = public_path('uploads/documents');
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $file->move($uploadDir, $filename);
                $fileUrl = asset('uploads/documents/' . $filename);

                $title = $request->title;
                // Use original filename if title is empty or if multiple files are uploaded
                if (empty($title) || count($request->file('files')) > 1) {
                    $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                }

                $data = $request->except(['files', 'title', 'file']);
                $data['title'] = $title;
                $data['file_url'] = $fileUrl;
                $data['year'] = date('Y');
                $data['category'] = $categoryName;
                $data['document_category_id'] = $request->document_category_id;

                \App\Models\PublicDocument::create($data);
                $uploadedCount++;
            }
        }

        $msg = $uploadedCount > 1 ? "$uploadedCount dokumen publik." : 'dokumen publik: ' . ($request->title ?: 'Tanpa Judul');
        Activity::log('Dokumen', 'Buat', 'Menambahkan ' . $msg);

        return redirect(route('admin.dashboard') . '#section-dokumen')->with('success', "$uploadedCount Dokumen berhasil ditambahkan!");
    }

    public function updateDocument(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'document_category_id' => 'required|exists:document_categories,id',
            'file' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $document = \App\Models\PublicDocument::findOrFail($id);
        $data = $request->except('file');

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $uploadDir = public_path('uploads/documents');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $file->move($uploadDir, $filename);
            $data['file_url'] = asset('uploads/documents/' . $filename);
        }

        // Backward compatibility for legacy category column
        $cat = \App\Models\DocumentCategory::find($data['document_category_id']);
        $data['category'] = $cat ? $cat->name : '-';

        $document->update($data);

        Activity::log('Dokumen', 'Update', 'Memperbarui dokumen publik: ' . $document->title);

        return redirect(route('admin.dashboard') . '#section-dokumen')->with('success', 'Dokumen berhasil diperbarui!');
    }

    public function deleteDocument($id)
    {
        $document = \App\Models\PublicDocument::findOrFail($id);
        Activity::log('Dokumen', 'Hapus', 'Menghapus dokumen publik: ' . $document->title);
        $document->delete();

        return redirect(route('admin.dashboard') . '#section-dokumen')->with('success', 'Dokumen berhasil dihapus!');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'gemini_model' => 'required|string|max:100',
        ]);

        $model = $request->input('gemini_model');

        \App\Models\Stat::updateOrCreate(
            ['key' => 'gemini_model'],
            [
                'value' => $model,
                'label' => 'Model AI Chatbot'
            ]
        );

        Activity::log('Setelan', 'Update', 'Memperbarui model AI Chatbot menjadi: ' . $model);

        return redirect(route('admin.dashboard') . '#section-setelan')->with('success', 'Setelan model AI berhasil diperbarui!');
    }

    public function destroyIngest($id)
    {
        $ingestion = DocumentIngestion::findOrFail($id);

        // Hapus file fisik dari storage jika ada
        $filePath = 'documents/' . $ingestion->file_name;
        if (Storage::disk('local')->exists($filePath)) {
            Storage::disk('local')->delete($filePath);
        }

        // Hapus semua chunk data chatbot yang memiliki document_name sesuai dengan original_name
        \App\Models\DocumentChunk::where('document_name', $ingestion->original_name)->delete();

        Activity::log('Chatbot', 'Hapus', 'Menghapus dokumen ingest: ' . $ingestion->original_name);

        // Hapus record ingestion
        $ingestion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen ingest berhasil dihapus.'
        ]);
    }

    public function cancelIngest($id)
    {
        $ingestion = DocumentIngestion::findOrFail($id);

        if ($ingestion->status === 'processing' || $ingestion->status === 'pending') {
            $ingestion->update([
                'status' => 'cancelled'
            ]);
            Activity::log('Chatbot', 'Batal', 'Membatalkan proses ingest: ' . $ingestion->original_name);
            return response()->json([
                'success' => true,
                'message' => 'Proses ingest berhasil dibatalkan.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Proses tidak dapat dibatalkan karena status: ' . $ingestion->status
        ], 400);
    }

    // ==========================================
    // GALLERY MANAGEMENT
    // ==========================================
    public function storeGallery(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imagePath = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            // Pindahkan ke folder public/images/gallery (agar sesuai dengan file yang sudah ada sebelumnya)
            $file->move(public_path('images/gallery'), $filename);
            $imagePath = $filename;
        }

        \App\Models\Gallery::create([
            'title' => $request->title,
            'location' => $request->location,
            'image_path' => $imagePath,
        ]);

        Activity::log('Galeri', 'Tambah', 'Menambah foto galeri baru: ' . $request->title);
        return redirect(route('admin.dashboard') . '#section-galeri')->with('success', 'Galeri berhasil ditambahkan!');
    }

    public function updateGallery(Request $request, $id)
    {
        $gallery = \App\Models\Gallery::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            $oldPath = public_path('images/gallery/' . $gallery->image_path);
            if (file_exists($oldPath) && is_file($oldPath)) {
                @unlink($oldPath);
            }

            $file = $request->file('image');
            $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/gallery'), $filename);
            $gallery->image_path = $filename;
        }

        $gallery->title = $request->title;
        $gallery->location = $request->location;
        $gallery->save();

        Activity::log('Galeri', 'Ubah', 'Mengubah data galeri: ' . $gallery->title);
        return redirect(route('admin.dashboard') . '#section-galeri')->with('success', 'Galeri berhasil diubah!');
    }

    public function deleteGallery($id)
    {
        $gallery = \App\Models\Gallery::findOrFail($id);

        $oldPath = public_path('images/gallery/' . $gallery->image_path);
        if (file_exists($oldPath) && is_file($oldPath)) {
            @unlink($oldPath);
        }

        $title = $gallery->title;
        $gallery->delete();

        Activity::log('Galeri', 'Hapus', 'Menghapus foto galeri: ' . $title);
        return redirect(route('admin.dashboard') . '#section-galeri')->with('success', 'Galeri berhasil dihapus!');
    }
}
