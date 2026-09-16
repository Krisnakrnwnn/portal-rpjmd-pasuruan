@extends('layouts.app')

@section('title', 'Beranda | Portal Informasi Perencanaan Daerah, Riset Dan Inovasi Kabupaten Pasuruan')
@section('meta_description', 'Portal Resmi Badan Perencanaan Pembangunan, Riset, dan Inovasi Daerah (Bapperida) Kabupaten Pasuruan — Akses data perencanaan, dokumen RPJMD resmi, dan berita pembangunan daerah secara transparan.')

@section('content')
    {{-- 1. Hero Section Navy --}}
    <section class="relative bg-[#102e56] text-white overflow-hidden min-h-[calc(100vh-5rem)] flex flex-col justify-center py-20 sm:py-28 lg:py-36 font-sans">
      {{-- Hero Background Image with Overlay (Tebal di kiri, Gradasi Transparan dari kanan ~70%) --}}
      <div class="absolute inset-0 z-0">
        <img src="{{ asset('hero.png') }}" alt="" class="w-full h-full object-cover object-right opacity-100" aria-hidden="true" />
        <div class="absolute inset-0" style="background: linear-gradient(to right, #102e56 0%, #102e56 30%, rgba(16, 46, 86, 0.9) 45%, rgba(16, 46, 86, 0.4) 80%, transparent 100%);"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#102e56]/60 via-transparent to-transparent sm:hidden"></div>
      </div>

      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="max-w-3xl">
          {{-- Eyebrow --}}
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-[#a5e2ed] text-xs font-semibold tracking-[0.14em] uppercase mb-6">
            <span class="w-2 h-2 rounded-full bg-[#a5e2ed]"></span>
            Kabupaten Pasuruan
          </div>

          {{-- Main Title --}}
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-semibold text-white tracking-tight leading-[1.15] mb-6">
            Portal Informasi Perencanaan Daerah, Riset Dan Inovasi
          </h1>

          {{-- Description --}}
          <p class="text-base sm:text-lg text-[#d6e3f2] leading-relaxed font-normal mb-8 max-w-2xl">
            Akses dokumen resmi RPJMD, rencana pembangunan daerah, publikasi capaian kinerja, dan inovasi pembangunan Bapperida Kabupaten Pasuruan secara transparan dan akuntabel.
          </p>

          {{-- Actions --}}
          <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('dokumen') }}"
              class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl bg-[#1d4ed8] hover:bg-[#1e40af] active:bg-[#1e3a8a] text-white font-medium text-sm sm:text-base shadow-sm transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#a5e2ed] focus-visible:ring-offset-2 focus-visible:ring-offset-[#102e56]">
              <svg class="w-5 h-5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <span>Lihat Dokumen</span>
            </a>

            <a href="{{ route('profil') }}"
              class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-transparent hover:bg-white/10 active:bg-white/15 text-white border border-white/30 font-medium text-sm sm:text-base transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#a5e2ed] focus-visible:ring-offset-2 focus-visible:ring-offset-[#102e56]">
              <span>Profil Instansi</span>
              <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    {{-- 2. Statistik Dinamis --}}
    @if(isset($heroStats) && $heroStats->isNotEmpty())
    <section class="relative z-20 -mt-12 sm:-mt-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 font-sans pb-24 sm:pb-32">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        @foreach($heroStats as $stat)
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-lg hover:shadow-xl transition-all duration-300">
          <p class="text-3xl sm:text-4xl font-bold text-[#102e56] tracking-tight leading-tight">{{ $stat->value }}</p>
          <p class="text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wider mt-2">{{ $stat->label }}</p>
        </div>
        @endforeach
      </div>
    </section>
    @endif
@endsection
