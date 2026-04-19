<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\News;
use App\Models\Activity;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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

        // Pisahkan: Statistik Hero Beranda vs Statistik Capaian RPJMD
        $heroStats = \App\Models\Stat::where('key', 'like', 'hero_%')->get();

        $capaianStats = \App\Models\Stat::where('key', 'not like', 'hero_%')->get();

        // Real Statistics for Cards
        $counts = [
            'news' => $news->count(),
            'unread_contacts' => \App\Models\Contact::where('status', 'unread')->count(),
            'services' => $services->count(),
            'users' => $users->count(),
            'sectors' => $sectors->count(),
        ];

        $activities = Activity::with('user')->orderBy('created_at', 'desc')->take(10)->get();

        return view('admin.dashboard', compact('news', 'contacts', 'services', 'heroStats', 'capaianStats', 'profiles', 'users', 'counts', 'activities', 'sectors'));
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
            $path = $request->file('image')->store('news', 'public');
            $imageUrl = 'storage/' . $path;
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
                $oldPath = str_replace('storage/', '', $news->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('image')->store('news', 'public');
            $data['image_url'] = 'storage/' . $path;
        }

        $news->update($data);

        Activity::log('Berita', 'Update', 'Memperbarui berita: ' . $news->title);

        return redirect(route('admin.dashboard') . '#section-berita')->with('success', 'Berita berhasil diperbarui!');
    }

    public function deleteNews($id)
    {
        $news = News::findOrFail($id);

        // Hapus file gambar dari storage jika ada
        if ($news->image_url) {
            $path = str_replace('storage/', '', $news->image_url);
            Storage::disk('public')->delete($path);
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

    public function updateProfile(Request $request)
    {
        foreach ($request->profiles as $key => $content) {
            \App\Models\Profile::where('key', $key)->update(['content' => $content]);
        }
        Activity::log('Profil', 'Update', 'Memperbarui konten profil instansi.');
        return redirect(route('admin.dashboard') . '#section-setelan')->with('success', 'Profil instansi berhasil diperbarui!');
    }

    public function resolveContact($id)
    {
        $contact = \App\Models\Contact::findOrFail($id);
        $contact->update(['status' => 'resolved']);
        Activity::log('Aspirasi', 'Selesai', 'Menandai selesai pesan dari: ' . $contact->name);
        return redirect(route('admin.dashboard') . '#section-layanan')->with('success', 'Aspirasi dari "' . $contact->name . '" ditandai selesai!');
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
}
