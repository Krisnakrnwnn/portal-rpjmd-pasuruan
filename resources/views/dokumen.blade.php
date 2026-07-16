@extends('layouts.app')

@section('title', 'Dokumen - Bapperida Kabupaten Pasuruan')

@push('styles')
<style>
    /* ===== GOOGLE DRIVE STYLE ===== */
    .gdrive-hidden { display: none !important; }

    /* --- Sidebar --- */
    .gdrive-sidebar {
        width: 220px;
        min-width: 220px;
        flex-shrink: 0;
        padding: 12px 8px;
    }

    .gdrive-sidebar-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 500;
        color: #202124;
        text-decoration: none;
        transition: background 0.15s;
        white-space: nowrap;
        overflow: hidden;
    }
    .gdrive-sidebar-item:hover { background: #e8eaed; }
    .gdrive-sidebar-item.active { background: #d3e3fd; color: #0842a0; font-weight: 700; }
    .gdrive-sidebar-section-label {
        font-size: 11px;
        font-weight: 700;
        color: #9aa0a6;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 12px 16px 4px;
    }

    /* --- Main Content --- */
    .gdrive-main { flex: 1; min-width: 0; }

    /* Toolbar */
    .gdrive-topbar {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #e8eaed;
        margin-bottom: 16px;
    }
    .gdrive-search {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f1f3f4;
        border: 1px solid transparent;
        border-radius: 24px;
        padding: 8px 16px;
        flex: 1;
        max-width: 480px;
        transition: all 0.2s;
    }
    .gdrive-search:focus-within {
        background: #fff;
        border-color: #1a73e8;
        box-shadow: 0 2px 8px rgba(26,115,232,0.12);
    }
    .gdrive-search input {
        border: none;
        background: transparent;
        outline: none;
        font-size: 14px;
        color: #202124;
        width: 100%;
    }
    .gdrive-view-btn {
        padding: 6px;
        border-radius: 4px;
        border: none;
        background: transparent;
        cursor: pointer;
        color: #5f6368;
        display: flex;
        align-items: center;
        transition: background 0.1s;
    }
    .gdrive-view-btn:hover { background: #e8eaed; }
    .gdrive-view-btn.active { color: #1a73e8; background: #d3e3fd; }

    /* Breadcrumb */
    .gdrive-breadcrumb {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 16px;
        font-weight: 700;
        color: #202124;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
    .gdrive-breadcrumb a { color: #5f6368; text-decoration: none; font-size: 14px; font-weight: 400; transition: color 0.1s; }
    .gdrive-breadcrumb a:hover { color: #1a73e8; text-decoration: underline; }
    .gdrive-breadcrumb .sep { color: #bdc1c6; }

    /* Section label */
    .gdrive-section-label {
        font-size: 12px; font-weight: 700; color: #5f6368;
        text-transform: uppercase; letter-spacing: 0.08em;
        margin-bottom: 10px; margin-top: 8px;
    }

    /* Year chips */
    .year-chip {
        padding: 4px 14px; border-radius: 999px;
        border: 1px solid #dadce0; background: #fff;
        font-size: 12px; font-weight: 600; color: #3c4043;
        cursor: pointer; transition: all 0.15s;
        text-decoration: none; display: inline-block;
    }
    .year-chip:hover { background: #e8eaed; border-color: #bdc1c6; }
    .year-chip.active { background: #d3e3fd; border-color: #1a73e8; color: #0842a0; }

    /* === GRID VIEW === */
    .view-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 8px;
    }
    .gdrive-card {
        background: #fff; border: 1px solid #e0e0e0; border-radius: 8px;
        padding: 16px 12px; cursor: pointer;
        transition: box-shadow 0.15s, border-color 0.15s, background 0.1s;
        text-decoration: none; display: flex; flex-direction: column;
        align-items: center; gap: 8px; text-align: center; overflow: hidden;
    }
    .gdrive-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.15); border-color: #bdc1c6; background: #f8f9fa; }
    .gdrive-card-label { font-size: 12px; color: #202124; font-weight: 500; max-width: 100%; width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .gdrive-card-meta { font-size: 11px; color: #9aa0a6; }

    /* === LIST VIEW === */
    .view-list { display: flex; flex-direction: column; }
    .view-list-header {
        display: flex; align-items: center; gap: 12px; padding: 6px 12px;
        border-bottom: 1px solid #e8eaed; font-size: 11px; font-weight: 700;
        color: #5f6368; text-transform: uppercase; letter-spacing: 0.06em;
    }
    .gdrive-list-item {
        display: flex; align-items: center; gap: 12px; padding: 8px 12px;
        border-radius: 4px; transition: background 0.1s;
        text-decoration: none; color: #202124; border-bottom: 1px solid #f1f3f4;
    }
    .gdrive-list-item:hover { background: #f1f3f4; }
    .gdrive-list-item .item-name { flex: 1; font-size: 13px; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .gdrive-list-item .item-meta { font-size: 12px; color: #9aa0a6; width: 80px; text-align: right; flex-shrink: 0; }

    /* Icons */
    .icon-folder { color: #fbbf24; }
    .icon-pdf    { color: #ea4335; }

    /* Empty state */
    .gdrive-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 20px; color: #9aa0a6; gap: 10px; text-align: center; }

    /* Divider between sidebar and main */
    .gdrive-layout { display: flex; gap: 0; }
    .gdrive-divider { width: 1px; background: #e8eaed; flex-shrink: 0; margin: 0 16px; }

    @media (max-width: 768px) {
        .gdrive-layout { flex-direction: column; }
        .gdrive-sidebar { width: 100%; min-width: 0; display: flex; flex-wrap: wrap; gap: 4px; padding: 8px 0; }
        .gdrive-sidebar-section-label { display: none; }
        .gdrive-divider { display: none; }
    }
</style>
@endpush

@section('content')

{{-- ========== HERO SECTION ========== --}}
<div class="relative w-full min-h-[450px] bg-blue-950 overflow-hidden mb-10 flex items-center">
    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500/30 rounded-full blur-[100px] animate-pulse"></div>
    <img src="{{ asset('hero.png') }}" class="absolute inset-0 w-full h-full object-cover opacity-30 mix-blend-overlay object-bottom" />
    <div class="absolute inset-0 bg-gradient-to-b from-[#041a42]/80 via-blue-900/60 to-[#0A3D91]"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-28 pb-24 text-center flex flex-col items-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 mb-6 text-sm text-blue-200">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors text-xs uppercase tracking-wider font-semibold">Beranda</a>
            <span>/</span>
            <span class="text-white text-xs uppercase tracking-wider font-bold">Dokumen Bapperida</span>
        </div>
        <h1 data-aos="fade-up" class="text-4xl md:text-6xl font-black text-white mb-6 drop-shadow-2xl">
            Repositori <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500">Publik</span>
        </h1>
        <p data-aos="fade-up" data-aos-delay="100" class="text-blue-100 text-lg md:text-xl max-w-3xl font-light leading-relaxed">
            Akses kumpulan dokumen resmi dan laporan publikasi Bapperida Kabupaten Pasuruan secara mudah dan transparan.
        </p>
    </div>

    <!-- Bottom Curve Divider -->
    <div class="absolute bottom-0 w-full overflow-hidden leading-none z-20 translate-y-[2px]">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-[50px] lg:h-[80px]">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C54,82.47,110.15,77.58,168.22,76.54,219.64,75.64,271.86,65.68,321.39,56.44Z" fill="#F9FAFB"></path>
        </svg>
    </div>
</div>

{{-- ========== DRIVE CONTENT ========== --}}
<section class="bg-white pb-24 min-h-[50vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Topbar: search + view toggle --}}
        <div class="gdrive-topbar">
            <div class="gdrive-search">
                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1111 5a6 6 0 016 6z"/>
                </svg>
                <input id="search-docs" type="text" placeholder="Cari dokumen...">
            </div>
            <div style="display:flex;gap:4px;margin-left:auto;">
                <button class="gdrive-view-btn active" id="btn-grid" onclick="setView('grid')" title="Tampilan Grid">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </button>
                <button class="gdrive-view-btn" id="btn-list" onclick="setView('list')" title="Tampilan List">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Layout: Sidebar + Main --}}
        <div class="gdrive-layout">

            {{-- ========== SIDEBAR ========== --}}
            <aside class="gdrive-sidebar">
                <a href="{{ route('dokumen') }}" class="gdrive-sidebar-item {{ !$currentCategoryModel ? 'active' : '' }}">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                    Semua Dokumen
                </a>
                <div class="gdrive-sidebar-section-label">Kategori</div>
                @php $rootCats = \App\Models\DocumentCategory::whereNull('parent_id')->orderBy('name')->get(); @endphp
                @foreach($rootCats as $rc)
                <a href="{{ route('dokumen', ['kategori' => $rc->slug]) }}"
                   class="gdrive-sidebar-item {{ ($currentCategoryModel && ($currentCategoryModel->id === $rc->id || optional($currentCategoryModel->parent)->id === $rc->id || optional(optional($currentCategoryModel->parent)->parent)->id === $rc->id)) ? 'active' : '' }}">
                    <svg class="w-5 h-5 flex-shrink-0 icon-folder" viewBox="0 0 24 24" fill="currentColor"><path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                    {{ $rc->name }}
                </a>
                @endforeach
            </aside>

            <div class="gdrive-divider"></div>

            {{-- ========== MAIN ========== --}}
            <div class="gdrive-main">

                {{-- Breadcrumb --}}
                <div class="gdrive-breadcrumb">
                    <a href="{{ route('dokumen') }}">Dokumen Bapperida</a>
                    @if($currentCategoryModel)
                        @foreach($breadcrumb as $crumb)
                            <span class="sep">›</span>
                            @if(!$loop->last)
                                <a href="{{ route('dokumen', ['kategori' => $crumb->slug]) }}">{{ $crumb->name }}</a>
                            @else
                                <span>{{ $crumb->name }}</span>
                            @endif
                        @endforeach
                    @endif
                </div>

                {{-- Filter Tahun --}}
                @if($currentCategoryModel && count($years) > 0)
                <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:16px;">
                    <span style="font-size:12px;color:#5f6368;font-weight:600;white-space:nowrap;">Tahun:</span>
                    @php $catSlug = $currentCategoryModel->slug; @endphp
                    <a href="{{ route('dokumen', ['kategori' => $catSlug]) }}" class="year-chip {{ !$tahun ? 'active' : '' }}">Semua</a>
                    @foreach($years as $yr)
                    <a href="{{ route('dokumen', ['kategori' => $catSlug, 'tahun' => $yr]) }}" class="year-chip {{ $tahun == $yr ? 'active' : '' }}">{{ $yr }}</a>
                    @endforeach
                </div>
                @endif

                {{-- ---- FOLDERS ---- --}}
                @if(isset($subCategories) && $subCategories->count() > 0)
                <div class="gdrive-section-label">Folder</div>

                <div id="folder-grid" class="view-grid" style="margin-bottom:24px;" data-view-target="folder">
                    @foreach($subCategories as $cat)
                    <a href="{{ route('dokumen', ['kategori' => $cat->slug]) }}" class="gdrive-card folder-card" data-name="{{ strtolower($cat->name) }}">
                        <svg class="w-12 h-12 icon-folder" viewBox="0 0 24 24" fill="currentColor"><path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                        <span class="gdrive-card-label" title="{{ $cat->name }}">{{ $cat->name }}</span>
                    </a>
                    @endforeach
                </div>

                <div id="folder-list" class="view-list gdrive-hidden" style="margin-bottom:24px;" data-view-target="folder">
                    @foreach($subCategories as $cat)
                    <a href="{{ route('dokumen', ['kategori' => $cat->slug]) }}" class="gdrive-list-item folder-card" data-name="{{ strtolower($cat->name) }}">
                        <svg class="w-5 h-5 icon-folder flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                        <span class="item-name">{{ $cat->name }}</span>
                        <span class="item-meta">Folder</span>
                    </a>
                    @endforeach
                </div>
                @endif

                {{-- ---- DOCUMENTS ---- --}}
                @if($currentCategoryModel)
                <div class="gdrive-section-label">File</div>

                <div id="docs-grid" class="view-grid" data-view-target="docs">
                    @foreach($dokumen as $doc)
                    <a href="{{ $doc->file_url }}" target="_blank" rel="noopener" class="gdrive-card doc-card" data-name="{{ strtolower($doc->title) }}">
                        <svg class="w-12 h-12 icon-pdf" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/></svg>
                        <span class="gdrive-card-label" title="{{ $doc->title }}">{{ $doc->title }}</span>
                        <span class="gdrive-card-meta">{{ $doc->year }}</span>
                    </a>
                    @endforeach
                </div>

                <div id="docs-list" class="view-list gdrive-hidden" data-view-target="docs">
                    @if($dokumen->count() > 0)
                    <div class="view-list-header">
                        <svg class="w-4 h-4 opacity-0 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0z" fill="none"/></svg>
                        <span style="flex:1;">Nama</span>
                        <span style="width:60px;text-align:center;">Tahun</span>
                        <span style="width:130px;text-align:right;">Terakhir Diubah</span>
                    </div>
                    @endif
                    @foreach($dokumen as $doc)
                    <a href="{{ $doc->file_url }}" target="_blank" rel="noopener" class="gdrive-list-item doc-card" data-name="{{ strtolower($doc->title) }}">
                        <svg class="w-5 h-5 icon-pdf flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/></svg>
                        <span class="item-name">{{ $doc->title }}</span>
                        <span style="width:60px;text-align:center;font-size:12px;color:#9aa0a6;flex-shrink:0;">{{ $doc->year }}</span>
                        <span style="width:130px;text-align:right;font-size:11px;color:#9aa0a6;flex-shrink:0;">{{ $doc->updated_at->diffForHumans() }}</span>
                    </a>
                    @endforeach
                </div>

                @if($dokumen->count() === 0)
                <div class="gdrive-empty">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#e0e0e0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p style="font-size:14px;font-weight:500;">Folder ini masih kosong</p>
                </div>
                @endif

                @if($dokumen->hasPages())
                <div style="margin-top:24px;">{{ $dokumen->links() }}</div>
                @endif
                @endif

                {{-- Root empty --}}
                @if(!$currentCategoryModel && (!isset($subCategories) || $subCategories->count() === 0))
                <div class="gdrive-empty" style="padding:80px 20px;">
                    <svg class="w-20 h-20 icon-folder" viewBox="0 0 24 24" fill="currentColor" style="opacity:0.15;"><path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                    <p style="font-size:15px;font-weight:500;color:#5f6368;">Pilih folder dari sidebar kiri</p>
                </div>
                @endif

            </div>{{-- /gdrive-main --}}
        </div>{{-- /gdrive-layout --}}
    </div>
</section>

@push('scripts')
<script>
function setView(mode) {
    const isGrid = mode === 'grid';
    ['folder', 'docs'].forEach(type => {
        const g = document.getElementById(type + '-grid');
        const l = document.getElementById(type + '-list');
        if (g) g.classList.toggle('gdrive-hidden', !isGrid);
        if (l) l.classList.toggle('gdrive-hidden', isGrid);
    });
    document.getElementById('btn-grid')?.classList.toggle('active', isGrid);
    document.getElementById('btn-list')?.classList.toggle('active', !isGrid);
    localStorage.setItem('docView', mode);
}
document.addEventListener('DOMContentLoaded', () => setView(localStorage.getItem('docView') || 'grid'));

document.getElementById('search-docs')?.addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.folder-card, .doc-card').forEach(card => {
        card.style.display = (!q || (card.dataset.name || '').includes(q)) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
