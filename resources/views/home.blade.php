@extends('layouts.app')

@section('title', 'Bapperida Kabupaten Pasuruan - Layanan Informasi RPJMD')
@section('meta_description', 'Portal Resmi Badan Perencanaan Pembangunan, Riset, dan Inovasi Daerah (Bapperida) Kabupaten Pasuruan — Akses data perencanaan, target pembangunan, dan capaian kinerja secara transparan.')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
@endpush

@section('content')
    <!-- Hero Section -->
    <div class="relative w-full min-h-[600px] h-auto lg:h-[700px] pb-24 lg:pb-0 overflow-hidden" id="hero-slider">
      <!-- Swiper Wrapper to prevent Swiper CSS from overriding position: absolute -->
      <div class="absolute inset-0 z-0 w-full h-full">
        <div class="swiper hero-swiper w-full h-full">
          <div class="swiper-wrapper">
            <div class="swiper-slide overflow-hidden">
              <img src="{{ asset('hero.png') }}" alt="Hero RPJMD 1" class="w-full h-full object-cover object-center animate-ken-burns" />
            </div>
            <div class="swiper-slide overflow-hidden">
              <img src="{{ asset('hero1.png') }}" alt="Hero RPJMD 2" class="w-full h-full object-cover object-center animate-ken-burns" />
            </div>
            <div class="swiper-slide overflow-hidden">
              <img src="{{ asset('hero2.png') }}" alt="Hero RPJMD 3" class="w-full h-full object-cover object-center animate-ken-burns" />
            </div>
            <div class="swiper-slide overflow-hidden">
              <img src="{{ asset('hero3.png') }}" alt="Hero RPJMD 4" class="w-full h-full object-cover object-center animate-ken-burns" />
            </div>
          </div>
        </div>
      </div>

      <div class="absolute inset-0 bg-gradient-to-r from-blue-900/95 via-blue-900/80 to-blue-900/40 z-10 pointer-events-none"></div>
      <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center pt-20">
        <div class="w-full text-left mt-8 lg:mt-0">
          <div
            class="inline-block px-4 py-1.5 rounded-full bg-blue-100/20 backdrop-blur-md border border-white/20 mb-6 font-medium text-blue-100 text-sm tracking-wide hero-reveal" style="animation-delay: 0.1s;">
            #PasuruanMajuBersamaBapperida
          </div>
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-6 leading-tight drop-shadow-md hero-reveal" style="animation-delay: 0.3s;">
            Menuju <br />
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500">Kabupaten Pasuruan</span>
            <br />Maju, Sejahtera dan Berkadilan
          </h1>
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 lg:gap-8 mb-8 w-full">
            <p class="text-base sm:text-xl text-blue-100 font-light leading-relaxed max-w-xl hero-reveal m-0" style="animation-delay: 0.5s;">
              Portal Informasi Perencanaan Daerah, Riset Dan Inovasi. Akses data perencanaan, target pembangunan, dan capaian kinerja daerah secara transparan.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 hero-reveal shrink-0 lg:ml-auto w-full sm:w-auto mt-6 lg:mt-0" style="animation-delay: 0.7s;">
              <a href="{{ route('dokumen') }}"
                class="w-full sm:w-auto px-6 py-3 sm:px-8 sm:py-4 rounded-full text-blue-900 font-bold bg-white hover:bg-yellow-400 shadow-lg transition-all hover:-translate-y-1 text-center whitespace-nowrap text-sm sm:text-base">
                Lihat Dokumen
              </a>
              <a href="{{ route('profil') }}"
                class="w-full sm:w-auto px-6 py-3 sm:px-8 sm:py-4 rounded-full text-white font-semibold bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 transition-all text-center whitespace-nowrap text-sm sm:text-base">
                Profil Instansi
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const swiper = new Swiper('.hero-swiper', {
      loop: true,
      effect: 'slide',
      speed: 1000,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
      simulateTouch: false, // Prevent swiping since there is an overlay on top
    });
  });
</script>
@endpush
@endsection
