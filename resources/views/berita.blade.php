@extends('layouts.app')

@section('title', 'Layanan Informasi RPJMD Kabupaten Pasuruan - Berita & Informasi')

@section('content')
    <!-- Header Spanduk -->
    <div class="relative w-full min-h-[450px] bg-blue-950 overflow-hidden flex items-center">
      <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] bg-yellow-500/20 rounded-full blur-[120px] animate-pulse"></div>
      <img src="{{ asset('hero.png') }}" class="absolute inset-0 w-full h-full object-cover opacity-20 mix-blend-overlay object-top" />
      <div class="absolute inset-0 bg-gradient-to-t from-[#0A3D91] via-blue-900/80 to-[#041a42]/90"></div>
      
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-16 pb-12 text-center flex flex-col items-center">
        <!-- Breadcrumbs UI -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 mb-6 text-sm text-blue-200">
          <a href="{{ route('home') }}" class="hover:text-white cursor-pointer transition-colors text-xs uppercase tracking-wider font-semibold">Beranda</a>
          <span>/</span>
          <span class="text-white text-xs uppercase tracking-wider font-bold">Berita & Informasi</span>
        </div>
        
        <h1 class="text-4xl md:text-6xl font-black text-white mb-6 drop-shadow-2xl">
          Berita & <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500">Informasi</span>
        </h1>
        <p class="text-blue-100 text-lg md:text-xl max-w-3xl font-light leading-relaxed">
          Informasi terkini seputar agenda strategis, capaian program RPJMD, dan inovasi pembangunan yang sedang berjalan di Kabupaten Pasuruan.
        </p>
      </div>

      <!-- Bottom Curve Divider -->
      <div class="absolute bottom-0 w-full overflow-hidden leading-none z-20 translate-y-[2px]">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-[50px] lg:h-[80px]">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C54,82.47,110.15,77.58,168.22,76.54,219.64,75.64,271.86,65.68,321.39,56.44Z" fill="#F9FAFB"></path>
        </svg>
      </div>
    </div>

    <!-- Functional Search Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 mb-12 relative z-30">
      <div class="max-w-2xl mx-auto">
        <form action="{{ route('berita') }}" method="GET" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-2 flex items-center gap-2">
          <div class="flex-grow relative group">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            <input type="text" 
              name="search"
              value="{{ $search ?? '' }}"
              class="w-full bg-transparent border-0 focus:ring-0 py-4 pl-12 pr-4 text-gray-700 placeholder-gray-400 font-medium" 
              placeholder="Cari berita atau pengumuman...">
          </div>
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition-all active:scale-95 shadow-md">
            Cari
          </button>
        </form>
        @if($search)
          <div class="mt-4 flex flex-col items-center gap-2">
             <p class="text-sm font-medium text-gray-500">
               Menampilkan hasil untuk: <span class="text-blue-600 font-bold">"{{ $search }}"</span>
             </p>
             <a href="{{ route('berita') }}" class="text-xs font-bold text-red-500 hover:underline uppercase tracking-tighter">Bersihkan Pencarian ×</a>
          </div>
        @else
          <div class="mt-3 flex justify-center gap-4 text-xs font-medium text-gray-400">
            <span>Populer:</span>
            <a href="{{ route('berita', ['search' => 'Musrenbang']) }}" class="hover:text-blue-600 transition-colors">#Musrenbang</a>
            <a href="{{ route('berita', ['search' => 'IKU']) }}" class="hover:text-blue-600 transition-colors">#ProgramIKU</a>
            <a href="{{ route('berita', ['search' => 'Infrastruktur']) }}" class="hover:text-blue-600 transition-colors">#Infrastruktur</a>
          </div>
        @endif
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 w-full">
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10">
        @foreach($allNews as $post)
        <a href="{{ route('berita.detail', ['slug' => $post->slug]) }}" data-aos="fade-up" data-aos-delay="{{ ($loop->iteration % 3) * 100 }}" class="group bg-white rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col h-full cursor-pointer">
          <div class="h-64 relative overflow-hidden">
            <img src="{{ Str::startsWith($post->image_url, 'http') ? $post->image_url : asset($post->image_url ?? 'news1.png') }}" alt="{{ $post->title }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-in-out" />
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/40 to-transparent"></div>
          </div>
          <div class="p-8 flex-grow flex flex-col">
            <div class="flex items-center gap-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">
              <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                {{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }}
              </span>
              <span class="flex items-center gap-1.5 text-blue-600">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                {{ $post->author->role ?? 'Admin' }}
              </span>
            </div>
            <h3 class="font-bold text-2xl mb-4 text-gray-900 group-hover:text-blue-600 transition-colors leading-snug">{{ $post->title }}</h3>
            <p class="text-gray-500 text-sm leading-relaxed line-clamp-3 mb-6">{{ Str::limit(strip_tags($post->content), 150) }}</p>
            <div class="mt-auto text-blue-600 font-semibold flex items-center text-sm group-hover:tracking-wider transition-all">
              Baca Sepenuhnya &rarr;
            </div>
          </div>
        </a>
        @endforeach
      </div>

      <div class="mt-12">
        {{ $allNews->links() }}
      </div>
    </div>
@endsection
