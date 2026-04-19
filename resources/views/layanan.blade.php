@extends('layouts.app')

@section('title', 'Layanan Informasi RPJMD Kota Pasuruan - Layanan')

@section('content')
    <!-- Header Spanduk -->
    <div class="relative w-full min-h-[450px] bg-blue-950 overflow-hidden mb-16 flex items-center">
      <div class="absolute bottom-[-20%] right-[-10%] w-[600px] h-[600px] bg-blue-400/20 rounded-full blur-[150px] animate-pulse"></div>
      <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-[#0A3D91] via-blue-900/80 to-[#041a42]/90"></div>
      
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-16 pb-12 text-center flex flex-col items-center">
        <!-- Breadcrumbs UI -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 mb-6 text-sm text-blue-200">
          <a href="{{ route('home') }}" class="hover:text-white cursor-pointer transition-colors text-xs uppercase tracking-wider font-semibold">Beranda</a>
          <span>/</span>
          <span class="text-white text-xs uppercase tracking-wider font-bold">Layanan Informasi</span>
        </div>
        
        <h1 class="text-4xl md:text-6xl font-black text-white mb-6 drop-shadow-2xl">
          Layanan <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500">Informasi RPJMD</span>
        </h1>
        <p class="text-blue-100 text-lg md:text-xl max-w-3xl font-light leading-relaxed">
          Satu portal untuk mengakses dokumen RPJMD, memantau capaian program prioritas, dan mendapatkan data pembangunan Kota Pasuruan secara transparan.
        </p>
      </div>

      <!-- Bottom Curve Divider -->
      <div class="absolute bottom-0 w-full overflow-hidden leading-none z-20 translate-y-[2px]">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-[50px] lg:h-[80px]">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C54,82.47,110.15,77.58,168.22,76.54,219.64,75.64,271.86,65.68,321.39,56.44Z" fill="#F9FAFB"></path>
        </svg>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 w-full">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($services as $service)
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 p-10 flex flex-col items-center text-center group cursor-pointer" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
          <div class="w-20 h-20 bg-blue-50 group-hover:bg-blue-600 text-blue-600 group-hover:text-white rounded-full flex items-center justify-center mb-6 transition-colors shadow-sm">
            @if($service->icon == 'document-report')
              <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            @elseif($service->icon == 'chart-bar')
              <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            @else
              <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
            @endif
          </div>
          <h3 class="font-extrabold text-2xl mb-4 text-gray-900">{{ $service->name }}</h3>
          <p class="text-gray-500 text-base mb-8 leading-relaxed">{{ $service->description }}</p>
          <a href="{{ $service->url }}" target="{{ Str::startsWith($service->url, 'http') ? '_blank' : '_self' }}" class="mt-auto inline-flex items-center justify-center w-full bg-blue-50 text-blue-600 font-semibold py-3 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
            Akses Layanan
          </a>
        </div>
        @endforeach
      </div>
    </div>
@endsection
