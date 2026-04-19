@extends('layouts.app')

@section('title', 'Layanan Informasi RPJMD Kota Pasuruan - Beranda')
@section('meta_description', 'Portal Resmi Layanan Informasi RPJMD Kota Pasuruan — Akses data perencanaan, target pembangunan, dan capaian kinerja Pemerintah Kota Pasuruan secara transparan.')

@section('content')
    <!-- Hero Section -->
    <div class="relative w-full h-[600px] lg:h-[700px] overflow-hidden">
      <div class="absolute inset-0">
        <img src="{{ asset('hero.png') }}" alt="Hero RPJMD"
          class="w-full h-full object-cover object-center animate-ken-burns" />
      </div>
      <div class="absolute inset-0 bg-gradient-to-r from-blue-900/95 via-blue-900/80 to-blue-900/40"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center">
        <div class="max-w-2xl text-left">
          <div
            class="inline-block px-4 py-1.5 rounded-full bg-blue-100/20 backdrop-blur-md border border-white/20 mb-6 font-medium text-blue-100 text-sm tracking-wide hero-reveal" style="animation-delay: 0.1s; opacity: 0;">
            #PasuruanMajuBersamaRPJMD
          </div>
          <h1 class="text-4xl sm:text-5xl lg:text-7xl font-black text-white mb-6 leading-[1.2] lg:leading-[1.1] drop-shadow-md hero-reveal" style="animation-delay: 0.3s; opacity: 0;">
            Membangun <br />
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500">Kota Pasuruan</span>
            <br />Lebih Maju
          </h1>
          <p class="text-base sm:text-xl text-blue-100 mb-8 font-light leading-relaxed max-w-xl hero-reveal" style="animation-delay: 0.5s; opacity: 0;">
            Portal resmi Layanan Informasi RPJMD Kota Pasuruan. Akses data perencanaan, target pembangunan, dan capaian kinerja daerah secara transparan dan akuntabel.
          </p>
          <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('layanan') }}"
              class="px-8 py-4 rounded-full text-blue-900 font-bold bg-white hover:bg-yellow-400 shadow-lg transition-all hover:-translate-y-1 text-center">
              Akses Layanan RPJMD
            </a>
            <a href="{{ route('berita') }}"
              class="px-8 py-4 rounded-full text-white font-semibold bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 transition-all text-center">
              Berita Kota
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-10">
      <div class="bg-white rounded-2xl shadow-xl border border-gray-100 px-6 py-8 shadow-blue-900/5">
        
        <div class="grid gap-6 text-center" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
          @forelse($heroStats as $index => $stat)
          <div data-aos="zoom-in" data-aos-delay="{{ 100 + ($index * 100) }}">
            <div class="text-4xl font-extrabold text-blue-600 mb-1">{{ $stat->value }}</div>
            <div class="text-sm text-gray-500 font-medium">{{ $stat->label }}</div>
          </div>
          @empty
          <div class="col-span-full py-4 text-gray-400 font-medium text-sm">Belum ada data statistik beranda.</div>
          @endforelse
        </div>

      </div>
    </div>

    <!-- Feature/Berita Singkat -->
    <div id="berita" class="max-w-7xl mx-auto py-16 md:py-24 px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row justify-between items-end mb-12">
        <div>
          <h2 class="text-sm font-bold text-blue-600 tracking-widest uppercase mb-2">Publikasi Kota Pasuruan</h2>
          <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900">Pembaruan & Berita Terkini</h3>
        </div>
        <a href="{{ route('berita') }}"
          class="hidden md:inline-flex items-center text-blue-600 font-semibold hover:text-blue-800 transition-colors group mt-4 md:mt-0">
          Lihat Semua Berita
          <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
          </svg>
        </a>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($latestNews as $post)
        <a href="{{ route('berita.detail', ['slug' => $post->slug]) }}" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}"
          class="card-premium group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full cursor-pointer">
          <div class="h-56 relative overflow-hidden">
            <img src="{{ Str::startsWith($post->image_url, 'http') ? $post->image_url : asset($post->image_url ?? 'news1.png') }}" alt="{{ $post->title }}"
              class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-in-out" />
            <div
              class="absolute inset-0 bg-gradient-to-t from-gray-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            </div>
            <div
              class="absolute top-4 left-4 bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider backdrop-blur-md">
              {{ $post->category }}</div>
          </div>
          <div class="p-6 flex-grow flex flex-col">
            <div class="text-xs font-semibold text-gray-400 mb-3 flex items-center gap-3">
              <span class="flex items-center">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                {{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }}
              </span>
              <span class="flex items-center gap-1.5 text-blue-600">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                {{ $post->author->role ?? 'Admin' }}
              </span>
            </div>
            <h3
              class="font-extrabold text-xl mb-3 text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2">
              {{ $post->title }}</h3>
            <p class="text-gray-500 text-sm line-clamp-3 mb-6">{{ Str::limit(strip_tags($post->content), 120) }}</p>
            <div
              class="mt-auto text-blue-600 font-semibold group-hover:text-blue-800 transition-colors flex items-center text-sm">
              Baca Selengkapnya
              <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </div>
          </div>
        </a>
        @endforeach
      </div>
    </div>
@endsection
