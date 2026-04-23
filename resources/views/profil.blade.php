@extends('layouts.app')

@section('title', 'Layanan Informasi RPJMD Kabupaten Pasuruan - Profil')

@section('content')
    <!-- Header Spanduk -->
    <div class="relative w-full min-h-[450px] bg-blue-950 overflow-hidden mb-16 flex items-center">
      <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500/30 rounded-full blur-[100px] animate-pulse"></div>
      <img src="{{ asset('hero.png') }}" class="absolute inset-0 w-full h-full object-cover opacity-30 mix-blend-overlay object-bottom" />
      <div class="absolute inset-0 bg-gradient-to-b from-[#041a42]/80 via-blue-900/60 to-[#0A3D91]"></div>
      
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-16 pb-12 text-center flex flex-col items-center">
        <!-- Breadcrumbs UI -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 mb-6 text-sm text-blue-200">
          <a href="{{ route('home') }}" class="hover:text-white cursor-pointer transition-colors text-xs uppercase tracking-wider font-semibold">Beranda</a>
          <span>/</span>
          <span class="text-white text-xs uppercase tracking-wider font-bold">Profil Layanan</span>
        </div>
        
        <h1 class="text-4xl md:text-6xl font-black text-white mb-6 drop-shadow-2xl">
          Profil <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500">RPJMD Pasuruan</span>
        </h1>
        <p class="text-blue-100 text-lg md:text-xl max-w-3xl font-light leading-relaxed">
          Mengenal lebih dekat latar belakang, dasar hukum, dan komitmen Pemerintah Kabupaten Pasuruan dalam mewujudkan pembangunan daerah yang terencana, transparan, dan berkelanjutan melalui RPJMD.
        </p>
      </div>

      <!-- Bottom Curve Divider -->
      <div class="absolute bottom-0 w-full overflow-hidden leading-none z-20 translate-y-[2px]">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-[50px] lg:h-[80px]">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C54,82.47,110.15,77.58,168.22,76.54,219.64,75.64,271.86,65.68,321.39,56.44Z" fill="#F9FAFB"></path>
        </svg>
      </div>
    </div>

    <!-- Konten Profil -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
      <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow border border-gray-100 p-6 md:p-12 mb-10" data-aos="fade-up">
        <h2 class="text-3xl font-extrabold text-blue-900 mb-6 flex items-center">
          <svg class="w-8 h-8 mr-3 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
          Sejarah Singkat
        </h2>
        <div class="text-gray-600 leading-relaxed mb-6 text-lg prose prose-blue max-w-none">
          {!! nl2br(e($profiles['sejarah'] ?? '')) !!}
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-sm hover:shadow-lg transition-shadow border border-blue-100 p-8 md:p-10 border-t-4 border-t-yellow-400" data-aos="fade-right" data-aos-delay="100">
          <h2 class="text-2xl font-extrabold text-blue-900 mb-4">Visi</h2>
          <p class="text-gray-700 font-medium italic text-lg leading-relaxed">"{{ $profiles['visi'] ?? '' }}"</p>
        </div>
        <div class="bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-sm hover:shadow-lg transition-shadow border border-blue-100 p-8 md:p-10 border-t-4 border-t-blue-600" data-aos="fade-left" data-aos-delay="200">
          <h2 class="text-2xl font-extrabold text-blue-900 mb-4">Misi</h2>
          <ul class="space-y-4 text-gray-700 font-medium list-none">
            @foreach(explode('|', $profiles['misi'] ?? '') as $misi)
            <li class="flex items-start">
              <span class="text-blue-500 mr-2 mt-1">✔</span> {{ $misi }}
            </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
@endsection
