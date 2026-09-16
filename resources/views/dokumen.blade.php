@extends('layouts.app')

@section('title', 'Dokumen Publik | Bapperida Kabupaten Pasuruan')
@section('meta_description', 'Repositori resmi dokumen perencanaan daerah, RPJMD, RKPD, dan publikasi Bapperida Kabupaten Pasuruan.')

@push('styles')
<style>
    .gdrive-hidden { display: none !important; }

    /* Custom Transitions & Shadows */
    .doc-hover-card {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .doc-hover-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -8px rgba(16, 46, 86, 0.12), 0 4px 8px -4px rgba(16, 46, 86, 0.06);
    }
</style>
@endpush

@section('content')

{{-- 1. Hero Section Navy --}}
<section class="relative bg-[#102e56] text-white overflow-hidden py-14 sm:py-18 lg:py-20 font-sans">
    {{-- Hero Background Image with Overlay --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('hero.png') }}" alt="" class="w-full h-full object-cover object-right opacity-40 mix-blend-luminosity" aria-hidden="true" />
        <div class="absolute inset-0 bg-gradient-to-r from-[#102e56] via-[#102e56]/95 to-[#102e56]/80"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            {{-- Breadcrumb Pill --}}
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-[#a5e2ed] text-xs font-semibold tracking-wider uppercase mb-5">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
                <span class="text-white/40">/</span>
                <span class="text-white">Dokumen Publik</span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-semibold text-white tracking-tight leading-[1.2] mb-4">
                Repositori Dokumen Perencanaan Daerah
            </h1>
            <p class="text-base sm:text-lg text-[#d6e3f2] leading-relaxed font-normal max-w-2xl">
                Akses dan unduh dokumen resmi RPJMD, rencana strategis, kajian pembangunan, dan laporan publikasi Bapperida Kabupaten Pasuruan secara transparan.
            </p>
        </div>
    </div>
</section>

{{-- 2. Document Repository Explorer Section --}}
<section class="bg-[#f8fafc] py-10 sm:py-14 min-h-[60vh] font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Main Container Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">

            {{-- Top Toolbar --}}
            <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
                {{-- Search Bar --}}
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1111 5a6 6 0 016 6z"/>
                        </svg>
                    </div>
                    <input
                        id="search-docs"
                        type="text"
                        placeholder="Cari nama folder atau dokumen..."
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#1d4ed8] focus:ring-2 focus:ring-blue-100 transition-all"
                    >
                </div>

                {{-- Right Controls: View Switch & Total Info --}}
                <div class="flex items-center justify-between sm:justify-end gap-3">
                    <span class="text-xs text-slate-500 font-medium hidden md:inline-block">
                        Format: <strong class="text-slate-700">PDF</strong>
                    </span>

                    {{-- View Switch Buttons --}}
                    <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200/80">
                        <button
                            type="button"
                            id="btn-grid"
                            onclick="setView('grid')"
                            class="inline-flex items-center justify-center p-2 rounded-lg text-slate-600 hover:text-slate-900 transition-colors"
                            title="Tampilan Grid"
                            aria-label="Tampilan Grid"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </button>
                        <button
                            type="button"
                            id="btn-list"
                            onclick="setView('list')"
                            class="inline-flex items-center justify-center p-2 rounded-lg text-slate-600 hover:text-slate-900 transition-colors"
                            title="Tampilan List"
                            aria-label="Tampilan List"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Explorer Body: Sidebar + Main Content --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 min-h-[500px]">

                {{-- Sidebar --}}
                <aside class="lg:col-span-3 border-b lg:border-b-0 lg:border-r border-slate-100 p-4 sm:p-5 bg-slate-50/50">
                    <div class="mb-4 px-2 flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kategori Dokumen</span>
                    </div>

                    <nav class="space-y-1">
                        {{-- All Documents Item --}}
                        <a href="{{ route('dokumen') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ !$currentCategoryModel ? 'bg-[#102e56] text-white shadow-sm font-semibold' : 'text-slate-700 hover:bg-slate-100' }}">
                            <svg class="w-4 h-4 flex-shrink-0 {{ !$currentCategoryModel ? 'text-[#a5e2ed]' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/>
                            </svg>
                            <span class="truncate">Semua Dokumen</span>
                        </a>

                        @php $rootCats = \App\Models\DocumentCategory::whereNull('parent_id')->orderBy('name')->get(); @endphp
                        @foreach($rootCats as $rc)
                            @php
                                $isActive = $currentCategoryModel && (
                                    $currentCategoryModel->id === $rc->id ||
                                    optional($currentCategoryModel->parent)->id === $rc->id ||
                                    optional(optional($currentCategoryModel->parent)->parent)->id === $rc->id
                                );
                            @endphp
                            <a href="{{ route('dokumen', ['kategori' => $rc->slug]) }}"
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ $isActive ? 'bg-[#102e56] text-white shadow-sm font-semibold' : 'text-slate-700 hover:bg-slate-100' }}">
                                <svg class="w-4 h-4 flex-shrink-0 {{ $isActive ? 'text-[#a5e2ed]' : 'text-amber-500' }}" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
                                </svg>
                                <span class="truncate">{{ $rc->name }}</span>
                            </a>
                        @endforeach
                    </nav>
                </aside>

                {{-- Main Workspace --}}
                <main class="lg:col-span-9 p-5 sm:p-8 flex flex-col justify-between">
                    <div>
                        {{-- Breadcrumb Bar --}}
                        <div class="flex items-center gap-2 text-sm text-slate-500 flex-wrap mb-6 pb-4 border-b border-slate-100">
                            <a href="{{ route('dokumen') }}" class="inline-flex items-center gap-1.5 font-medium text-slate-600 hover:text-[#1d4ed8] transition-colors">
                                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                                </svg>
                                <span>Dokumen</span>
                            </a>

                            @if($currentCategoryModel)
                                @foreach($breadcrumb as $crumb)
                                    <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    @if(!$loop->last)
                                        <a href="{{ route('dokumen', ['kategori' => $crumb->slug]) }}" class="font-medium text-slate-600 hover:text-[#1d4ed8] transition-colors">
                                            {{ $crumb->name }}
                                        </a>
                                    @else
                                        <span class="font-semibold text-slate-900 bg-slate-100 px-2.5 py-0.5 rounded-lg text-xs">
                                            {{ $crumb->name }}
                                        </span>
                                    @endif
                                @endforeach
                            @endif
                        </div>

                        {{-- Year Filter Pills --}}
                        @if($currentCategoryModel && count($years) > 0)
                        <div class="mb-6 flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider mr-1">Tahun:</span>
                            @php $catSlug = $currentCategoryModel->slug; @endphp
                            <a href="{{ route('dokumen', ['kategori' => $catSlug]) }}"
                               class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-colors {{ !$tahun ? 'bg-[#1d4ed8] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                Semua Tahun
                            </a>
                            @foreach($years as $yr)
                            <a href="{{ route('dokumen', ['kategori' => $catSlug, 'tahun' => $yr]) }}"
                               class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-colors {{ $tahun == $yr ? 'bg-[#1d4ed8] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                {{ $yr }}
                            </a>
                            @endforeach
                        </div>
                        @endif

                        {{-- FOLDERS SECTION --}}
                        @if(isset($subCategories) && $subCategories->count() > 0)
                        <div class="mb-8" data-section="folder">
                            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Sub Folder</h2>

                            {{-- Folder Grid --}}
                            <div id="folder-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5" data-view-target="folder">
                                @foreach($subCategories as $cat)
                                <a href="{{ route('dokumen', ['kategori' => $cat->slug]) }}"
                                   class="folder-card doc-hover-card flex items-center gap-3.5 p-4 rounded-xl border border-slate-200/80 bg-white hover:border-slate-300 shadow-xs"
                                   data-name="{{ strtolower($cat->name) }}">
                                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-slate-900 truncate" title="{{ $cat->name }}">
                                            {{ $cat->name }}
                                        </p>
                                        <span class="text-[11px] text-slate-400 font-medium">Folder Dokumen</span>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                                @endforeach
                            </div>

                            {{-- Folder List --}}
                            <div id="folder-list" class="space-y-1.5 gdrive-hidden" data-view-target="folder">
                                @foreach($subCategories as $cat)
                                <a href="{{ route('dokumen', ['kategori' => $cat->slug]) }}"
                                   class="folder-card flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-white hover:bg-slate-50 transition-colors"
                                   data-name="{{ strtolower($cat->name) }}">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
                                        </svg>
                                        <span class="text-sm font-semibold text-slate-800 truncate">{{ $cat->name }}</span>
                                    </div>
                                    <span class="text-xs text-slate-400">Folder</span>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- DOCUMENTS / FILES SECTION --}}
                        @if($currentCategoryModel)
                        <div class="mb-4" data-section="docs">
                            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Daftar Dokumen</h2>

                            {{-- Documents Grid --}}
                            <div id="docs-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4" data-view-target="docs">
                                @foreach($dokumen as $doc)
                                <div class="doc-card doc-hover-card flex flex-col justify-between p-5 rounded-2xl border border-slate-200/90 bg-white shadow-xs"
                                     data-name="{{ strtolower($doc->title) }}">
                                    <div>
                                        <div class="flex items-center justify-between gap-2 mb-3">
                                            <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/>
                                                </svg>
                                            </div>
                                            @if($doc->year)
                                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[11px] font-semibold">
                                                {{ $doc->year }}
                                            </span>
                                            @endif
                                        </div>

                                        <h3 class="text-sm font-semibold text-slate-900 leading-snug line-clamp-2 mb-3" title="{{ $doc->title }}">
                                            <a href="{{ $doc->file_url }}" target="_blank" rel="noopener" class="hover:text-[#1d4ed8] transition-colors focus:outline-none">
                                                {{ $doc->title }}
                                            </a>
                                        </h3>
                                    </div>

                                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                        <span class="text-[11px] text-slate-400 font-medium">
                                            {{ $doc->updated_at ? $doc->updated_at->diffForHumans() : 'Tersedia' }}
                                        </span>
                                        <a href="{{ $doc->file_url }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1 text-xs font-semibold text-[#1d4ed8] hover:text-[#1e40af] transition-colors">
                                            <span>Buka</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            {{-- Documents List View --}}
                            <div id="docs-list" class="gdrive-hidden divide-y divide-slate-100 border border-slate-200/80 rounded-xl overflow-hidden bg-white" data-view-target="docs">
                                @if($dokumen->count() > 0)
                                <div class="bg-slate-50 px-4 py-2.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-4">
                                    <span class="flex-1">Nama Dokumen</span>
                                    <span class="w-20 text-center">Tahun</span>
                                    <span class="w-32 text-right hidden sm:block">Pembaruan</span>
                                    <span class="w-16 text-right">Aksi</span>
                                </div>
                                @endif

                                @foreach($dokumen as $doc)
                                <div class="doc-card flex items-center gap-4 px-4 py-3 hover:bg-slate-50/80 transition-colors"
                                     data-name="{{ strtolower($doc->title) }}">
                                    <div class="flex items-center gap-3 min-w-0 flex-1">
                                        <svg class="w-5 h-5 text-red-500 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/>
                                        </svg>
                                        <a href="{{ $doc->file_url }}" target="_blank" rel="noopener" class="text-sm font-medium text-slate-800 hover:text-[#1d4ed8] truncate transition-colors">
                                            {{ $doc->title }}
                                        </a>
                                    </div>
                                    <span class="w-20 text-center text-xs font-semibold text-slate-500 flex-shrink-0">
                                        {{ $doc->year ?? '-' }}
                                    </span>
                                    <span class="w-32 text-right text-xs text-slate-400 flex-shrink-0 hidden sm:block">
                                        {{ $doc->updated_at ? $doc->updated_at->diffForHumans() : '-' }}
                                    </span>
                                    <div class="w-16 text-right flex-shrink-0">
                                        <a href="{{ $doc->file_url }}" target="_blank" rel="noopener" class="text-xs font-semibold text-[#1d4ed8] hover:underline">
                                            Unduh
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            {{-- Empty State (Folder Kosong) --}}
                            @if($dokumen->count() === 0)
                            <div class="p-12 text-center bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-semibold text-slate-800 mb-1">Belum Ada Dokumen</h3>
                                <p class="text-xs text-slate-500">Belum ada file dokumen yang diunggah pada folder ini.</p>
                            </div>
                            @endif

                            {{-- Pagination --}}
                            @if($dokumen->hasPages())
                            <div class="mt-6 pt-4 border-t border-slate-100">
                                {{ $dokumen->links() }}
                            </div>
                            @endif
                        </div>
                        @endif

                        {{-- Root Empty State --}}
                        @if(!$currentCategoryModel && (!isset($subCategories) || $subCategories->count() === 0))
                        <div class="py-16 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-blue-50 text-[#1d4ed8] mx-auto flex items-center justify-center mb-4">
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-slate-900 mb-1">Pilih Kategori Dokumen</h3>
                            <p class="text-sm text-slate-500 max-w-sm mx-auto">Silakan pilih salah satu kategori di sidebar sebelah kiri untuk melihat daftar dokumen.</p>
                        </div>
                        @endif
                    </div>
                </main>
            </div>
        </div>

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

    const btnGrid = document.getElementById('btn-grid');
    const btnList = document.getElementById('btn-list');

    if (btnGrid && btnList) {
        if (isGrid) {
            btnGrid.className = 'inline-flex items-center justify-center p-2 rounded-lg bg-white text-[#1d4ed8] shadow-xs font-semibold';
            btnList.className = 'inline-flex items-center justify-center p-2 rounded-lg text-slate-600 hover:text-slate-900 transition-colors';
        } else {
            btnGrid.className = 'inline-flex items-center justify-center p-2 rounded-lg text-slate-600 hover:text-slate-900 transition-colors';
            btnList.className = 'inline-flex items-center justify-center p-2 rounded-lg bg-white text-[#1d4ed8] shadow-xs font-semibold';
        }
    }
    localStorage.setItem('docView', mode);
}

document.addEventListener('DOMContentLoaded', () => {
    setView(localStorage.getItem('docView') || 'grid');
});

document.getElementById('search-docs')?.addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.folder-card, .doc-card').forEach(card => {
        card.style.display = (!q || (card.dataset.name || '').includes(q)) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
