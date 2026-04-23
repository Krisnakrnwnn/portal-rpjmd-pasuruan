@extends('layouts.app')

@section('title', 'Layanan Informasi RPJMD Kabupaten Pasuruan - Beranda')
@section('meta_description', 'Portal Resmi Layanan Informasi RPJMD Kabupaten Pasuruan — Akses data perencanaan, target pembangunan, dan capaian kinerja Pemerintah Kabupaten Pasuruan secara transparan.')

@section('content')
    <!-- Hero Section -->
    <div class="relative w-full min-h-[600px] h-auto lg:h-[700px] pb-24 lg:pb-0 overflow-hidden">
      <div class="absolute inset-0">
        <img src="{{ asset('hero.png') }}" alt="Hero RPJMD"
          class="w-full h-full object-cover object-center animate-ken-burns" />
      </div>
      <div class="absolute inset-0 bg-gradient-to-r from-blue-900/95 via-blue-900/80 to-blue-900/40"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center pt-20">
        <div class="w-full text-left mt-8 lg:mt-0">
          <div
            class="inline-block px-4 py-1.5 rounded-full bg-blue-100/20 backdrop-blur-md border border-white/20 mb-6 font-medium text-blue-100 text-sm tracking-wide hero-reveal" style="animation-delay: 0.1s; opacity: 0;">
            #PasuruanMajuBersamaRPJMD
          </div>
          <h1 class="text-4xl sm:text-5xl lg:text-7xl font-black text-white mb-6 leading-[1.2] lg:leading-[1.1] drop-shadow-md hero-reveal" style="animation-delay: 0.3s; opacity: 0;">
            Membangun <br />
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500">Kabupaten Pasuruan</span>
            <br />Lebih Maju
          </h1>
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 lg:gap-8 mb-8 w-full">
            <p class="text-base sm:text-xl text-blue-100 font-light leading-relaxed max-w-xl hero-reveal m-0" style="animation-delay: 0.5s; opacity: 0;">
              Portal resmi Layanan Informasi RPJMD Kabupaten Pasuruan. Akses data perencanaan, target pembangunan, dan capaian kinerja daerah secara transparan dan akuntabel.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 hero-reveal shrink-0 lg:ml-auto w-full sm:w-auto mt-6 lg:mt-0" style="animation-delay: 0.7s; opacity: 0;">
              <a href="{{ route('layanan') }}"
                class="w-full sm:w-auto px-6 py-3 sm:px-8 sm:py-4 rounded-full text-blue-900 font-bold bg-white hover:bg-yellow-400 shadow-lg transition-all hover:-translate-y-1 text-center whitespace-nowrap text-sm sm:text-base">
                Akses Layanan RPJMD
              </a>
              <a href="{{ route('berita') }}"
                class="w-full sm:w-auto px-6 py-3 sm:px-8 sm:py-4 rounded-full text-white font-semibold bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 transition-all text-center whitespace-nowrap text-sm sm:text-base">
                Berita Kota
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 sm:-mt-20 relative z-10">
      <div class="bg-white rounded-2xl md:rounded-3xl shadow-xl border border-gray-100 p-4 sm:p-8 lg:p-10 shadow-blue-900/5">
        
          <style>
            .custom-scroll::-webkit-scrollbar { height: 4px; }
            .custom-scroll::-webkit-scrollbar-track { background: transparent; border-radius: 10px; }
            .custom-scroll::-webkit-scrollbar-thumb { background: rgba(59,130,246,0.3); border-radius: 10px; }
            .custom-scroll::-webkit-scrollbar-thumb:hover { background: rgba(59,130,246,0.6); }
            @keyframes bounceHorizontal {
              0%, 100% { transform: translateX(0); }
              50% { transform: translateX(4px); }
            }
            .animate-bounce-horizontal { animation: bounceHorizontal 1.5s infinite ease-in-out; }
          </style>

          @if(count($heroStats) > 3)
          <div class="absolute right-3 top-2 md:hidden z-20">
            <div class="inline-flex items-center text-[8px] text-blue-600 font-extrabold tracking-widest uppercase bg-blue-50/80 px-2 py-0.5 rounded-full shadow-sm animate-pulse">
              Geser
              <svg class="w-2.5 h-2.5 ml-1 animate-bounce-horizontal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
            </div>
          </div>
          @endif

        <div class="flex flex-nowrap items-start gap-2 sm:gap-6 md:gap-12 lg:gap-16 text-center w-full overflow-x-auto snap-x snap-mandatory pb-4 pt-1 scroll-smooth custom-scroll" style="scrollbar-width: thin; scrollbar-color: rgba(59,130,246,0.3) transparent;">
          @forelse($heroStats as $index => $stat)
          <div data-aos="zoom-in" data-aos-delay="{{ 100 + ($index * 100) }}" class="flex-1 md:flex-none min-w-[75px] sm:min-w-[120px] snap-center md:first:ml-auto md:last:mr-auto">
            <div class="text-xl sm:text-4xl lg:text-5xl font-extrabold text-blue-600 mb-0.5 lg:mb-2 truncate">{{ $stat->value }}</div>
            <div class="text-[9px] sm:text-sm lg:text-base text-gray-500 font-bold tracking-tighter uppercase leading-[1.1]">{{ $stat->label }}</div>
          </div>
          @empty
          <div class="col-span-full py-4 text-gray-400 font-medium text-sm">Belum ada data statistik beranda.</div>
          @endforelse
        </div>

      </div>
    </div>

    <!-- Feature/Berita Singkat -->
    <div id="berita" class="max-w-7xl mx-auto pt-24 pb-24 px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row justify-between items-end mb-12">
        <div>
          <h2 class="text-sm font-bold text-blue-600 tracking-widest uppercase mb-2">Publikasi Kabupaten Pasuruan</h2>
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
          class="card-premium group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full cursor-pointer hover:shadow-lg transition-shadow">
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
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
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
