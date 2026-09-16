@extends('layouts.app')

@section('seo')
    <x-seo
        title="Profil Bapperida Kabupaten Pasuruan"
        description="Badan Perencanaan Pembangunan, Riset, dan Inovasi Daerah (Bapperida) Kabupaten Pasuruan - Visi, Misi, Struktur Organisasi, dan Tugas Pokok Fungsi dalam mendukung pembangunan daerah."
        keywords="Profil Bapperida, Visi Misi, Struktur Organisasi, Tupoksi, Kabupaten Pasuruan, Perencanaan Pembangunan"
    />
@endsection

@section('content')
    {{-- 1. Hero Section Navy (Sesuai dengan Dokumen) --}}
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
            <span class="text-white">Profil Instansi</span>
          </div>

          {{-- Main Title --}}
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-semibold text-white tracking-tight leading-[1.2] mb-4">
            Profil Bapperida Kabupaten Pasuruan
          </h1>

          {{-- Original Description --}}
          <p class="text-base sm:text-lg text-[#d6e3f2] leading-relaxed font-normal max-w-2xl">
            Mengenal lebih dekat Badan Perencanaan Pembangunan, Riset, dan Inovasi Daerah (Bapperida) Kabupaten Pasuruan — Lembaga yang bertanggung jawab atas perencanaan, riset, dan inovasi pembangunan daerah yang berkelanjutan.
          </p>
        </div>
      </div>
    </section>

    {{-- 2. Konten Profil Section --}}
    <section class="bg-[#f8fafc] py-12 sm:py-16 font-sans min-h-[50vh]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

        {{-- Sejarah Singkat (Jika Ada) --}}
        @if(!empty($profiles['sejarah']))
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-10">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#1d4ed8] flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div>
              <p class="text-xs font-bold text-[#1d4ed8] uppercase tracking-wider">Tentang Pasuruan</p>
              <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Sejarah Singkat</h2>
            </div>
          </div>
          <div class="text-slate-600 leading-relaxed text-sm sm:text-base space-y-4 font-normal">
            @foreach(explode("\n\n", $profiles['sejarah']) as $paragraph)
              <p>{{ $paragraph }}</p>
            @endforeach
          </div>
        </div>
        @endif

        {{-- Visi & Misi Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

          {{-- Card Visi --}}
          <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8 flex flex-col justify-between h-full relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none"></div>
            <div>
              <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#1d4ed8] flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-xs font-bold text-[#1d4ed8] uppercase tracking-wider">Arah Pembangunan</p>
                  <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Visi Pembangunan</h2>
                </div>
              </div>

              <div class="relative bg-slate-50 border border-slate-200/60 rounded-xl p-6 mb-4">
                <svg class="w-8 h-8 text-blue-200 absolute top-4 right-4 opacity-50" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                </svg>
                <p class="text-slate-800 text-lg sm:text-xl font-semibold leading-relaxed relative z-10 italic">
                  "{{ $profiles['visi'] ?? 'Terwujudnya Kabupaten Pasuruan yang Maju, Sejahtera, dan Berkeadilan' }}"
                </p>
              </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center gap-2 text-xs text-slate-400">
              <span class="inline-block w-2 h-2 rounded-full bg-[#1d4ed8]"></span>
              <span>Rencana Pembangunan Jangka Menengah Daerah</span>
            </div>
          </div>

          {{-- Card Misi --}}
          <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#1d4ed8] flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div>
                <p class="text-xs font-bold text-[#1d4ed8] uppercase tracking-wider">Komitmen Pelaksanaan</p>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Misi Pembangunan</h2>
              </div>
            </div>

            <div class="space-y-3.5">
              @php
                $misiList = array_filter(explode('|', $profiles['misi'] ?? ''));
              @endphp
              @forelse($misiList as $index => $misi)
              <div class="flex items-start gap-3.5 p-3.5 sm:p-4 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-slate-50 transition-colors">
                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-[#1d4ed8] text-white text-xs font-bold flex items-center justify-center mt-0.5 shadow-xs">
                  {{ $index + 1 }}
                </span>
                <p class="text-slate-700 text-sm sm:text-base leading-relaxed font-normal">
                  {{ trim($misi) }}
                </p>
              </div>
              @empty
              <p class="text-sm text-slate-500 italic">Misi belum tersedia.</p>
              @endforelse
            </div>
          </div>

        </div>

        {{-- Callout ke Dokumen Publik --}}
        <div class="bg-gradient-to-r from-[#102e56] to-[#1e40af] rounded-2xl p-6 sm:p-8 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
          <div class="max-w-xl text-center md:text-left">
            <h3 class="text-lg sm:text-xl font-semibold mb-1">Pelajari Dokumen Perencanaan Selengkapnya</h3>
            <p class="text-sm text-[#d6e3f2]">Akses seluruh dokumen RPJMD, RKPD, dan publikasi statistik Bapperida Kabupaten Pasuruan.</p>
          </div>
          <a href="{{ route('dokumen') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-[#102e56] font-semibold text-sm hover:bg-blue-50 transition-colors shadow-sm whitespace-nowrap">
            <svg class="w-4 h-4 text-[#1d4ed8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Buka Dokumen Publik</span>
          </a>
        </div>

      </div>
    </section>
@endsection
