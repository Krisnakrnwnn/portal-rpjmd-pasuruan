<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\News;
use App\Models\Service;

class PortalController extends Controller
{
    public function home()
    {
        $latestNews = News::with('author')->published()->orderBy('published_at', 'desc')->take(3)->get();
        $services = Service::all();
        
        // Ambil SEMUA statistik utama untuk hero section (dinamis jumlahnya)
        $heroStats = \App\Models\Stat::where('key', 'like', 'hero_%')->get();

        return view('home', compact('latestNews', 'services', 'heroStats'));
    }

    public function profil()
    {
        $profiles = \App\Models\Profile::all()->pluck('content', 'key');
        return view('profil', compact('profiles'));
    }

    public function berita(Request $request)
    {
        $search = $request->query('search');
        $query = News::with('author')->published(); // hanya berita publik

        if ($search) {
            $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($search) . '%']);
        }

        $allNews = $query->orderBy('published_at', 'desc')->paginate(9)->withQueryString();
        return view('berita', compact('allNews', 'search'));
    }

    public function beritaDetail($slug)
    {
        $news = News::with('author')->where('slug', $slug)->firstOrFail();

        // Berita draft tidak bisa diakses publik — tampilkan 404
        if (!$news->is_published) {
            abort(404);
        }

        // Pass SEO metadata
        $meta_description = \Str::limit(strip_tags($news->content), 160);
        $og_image = $news->image_url ? ( \Str::startsWith($news->image_url, 'http') ? $news->image_url : asset($news->image_url) ) : asset('hero.png');

        return view('berita.detail', compact('news', 'meta_description', 'og_image'));
    }

    public function layanan()
    {
        $services = \App\Models\Service::all();
        return view('layanan', compact('services'));
    }

    public function kontak()
    {
        return view('kontak');
    }

    public function capaian()
    {
        $statsRaw = \App\Models\Stat::all();
        $stats = [];
        foreach ($statsRaw as $s) {
            $stats[$s->key] = $s->value;
        }

        $sectors = \App\Models\Sector::with('indicators')->get();

        // Ambil waktu update terakhir dari tabel terkait
        $lastUpdate = collect([
            \App\Models\Indicator::max('updated_at'),
            \App\Models\Sector::max('updated_at'),
            \App\Models\Stat::max('updated_at'),
            \App\Models\Activity::max('created_at'),
        ])->filter()->max();

        $lastUpdate = $lastUpdate ? \Carbon\Carbon::parse($lastUpdate) : now();

        return view('capaian', compact('stats', 'sectors', 'lastUpdate'));
    }

    public function storeContact(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);

        \App\Models\Contact::create($data);

        return back()->with('success', 'Pesan Anda telah berhasil dikirim ke Admin!');
    }
}
