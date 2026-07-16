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

    // The standalone dokumen route is removed.

    public function galeri()
    {
        $galleries = \App\Models\Gallery::orderBy('created_at', 'desc')->paginate(24);
        return view('galeri', compact('galleries'));
    }

    public function kontak()
    {
        return view('kontak');
    }

    public function capaian(Request $request)
    {
        $kategoriSlug = $request->query('kategori');
        $tahun        = $request->query('tahun');

        $query = \App\Models\PublicDocument::query();
        $kategori = null;
        $currentCategoryModel = null;
        $subCategories = collect();
        $breadcrumb = [];

        if ($kategoriSlug) {
            $kategoriModel = \App\Models\DocumentCategory::where('slug', $kategoriSlug)->first();
            if ($kategoriModel) {
                $query->where('document_category_id', $kategoriModel->id);
                $kategori = $kategoriModel->name;
                $currentCategoryModel = $kategoriModel;
                $subCategories = $kategoriModel->children()->orderBy('name')->get();

                // Build breadcrumb
                $curr = $kategoriModel;
                while ($curr) {
                    array_unshift($breadcrumb, $curr);
                    $curr = $curr->parent;
                }
            } else {
                $query->where('category', $kategoriSlug);
                $kategori = strtoupper($kategoriSlug);
            }
        } else {
            // Root categories
            $subCategories = \App\Models\DocumentCategory::whereNull('parent_id')->orderBy('name')->get();
        }

        if ($tahun) {
            $query->where('year', $tahun);
        }

        $dokumen = $query->orderBy('year', 'desc')->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Tahun hanya dari kategori aktif (bukan semua dokumen)
        $yearsQuery = \App\Models\PublicDocument::select('year')->distinct()->orderBy('year', 'desc');
        if ($currentCategoryModel) {
            $yearsQuery->where('document_category_id', $currentCategoryModel->id);
        }
        $years = $yearsQuery->pluck('year');

        $lastUpdate = \App\Models\PublicDocument::max('updated_at');
        $lastUpdate = $lastUpdate ? \Carbon\Carbon::parse($lastUpdate) : now();

        return view('capaian', compact(
            'dokumen', 'kategori', 'tahun',
            'years', 'lastUpdate',
            'subCategories', 'breadcrumb', 'currentCategoryModel'
        ));
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
