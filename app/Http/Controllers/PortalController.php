<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use App\Models\Profile;
use App\Models\PublicDocument;
use App\Models\Stat;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function home()
    {
        // Ambil SEMUA statistik utama untuk hero section (dinamis jumlahnya)
        $heroStats = Stat::where('key', 'like', 'hero_%')->get();

        return view('home', compact('heroStats'));
    }

    public function profil()
    {
        $profiles = Profile::all()->pluck('content', 'key');

        return view('profil', compact('profiles'));
    }

    public function dokumen(Request $request)
    {
        $kategoriSlug = $request->query('kategori');
        $tahun = $request->query('tahun');

        $query = PublicDocument::query();
        $kategori = null;
        $currentCategoryModel = null;
        $subCategories = collect();
        $breadcrumb = [];

        if ($kategoriSlug) {
            $kategoriModel = DocumentCategory::where('slug', $kategoriSlug)->first();
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
            $subCategories = DocumentCategory::whereNull('parent_id')->orderBy('name')->get();
        }

        if ($tahun) {
            $query->where('year', $tahun);
        }

        $dokumen = $query->orderBy('year', 'desc')->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Tahun hanya dari kategori aktif (bukan semua dokumen)
        $yearsQuery = PublicDocument::select('year')->distinct()->orderBy('year', 'desc');
        if ($currentCategoryModel) {
            $yearsQuery->where('document_category_id', $currentCategoryModel->id);
        }
        $years = $yearsQuery->pluck('year');

        $lastUpdate = PublicDocument::max('updated_at');
        $lastUpdate = $lastUpdate ? Carbon::parse($lastUpdate) : now();

        return view('dokumen', compact(
            'dokumen', 'kategori', 'tahun',
            'years', 'lastUpdate',
            'subCategories', 'breadcrumb', 'currentCategoryModel'
        ));
    }
}
