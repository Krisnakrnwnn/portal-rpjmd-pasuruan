<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- PWA Meta Tags -->
  <link rel="manifest" href="{{ asset('manifest.json') }}">
  <meta name="theme-color" content="#2563eb">
  <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

  <title>@yield('title', 'Layanan Informasi RPJMD Kabupaten Pasuruan')</title>
  <meta name="description" content="@yield('meta_description', 'Portal Layanan Informasi RPJMD Kabupaten Pasuruan. Transparansi data, perencanaan, dan capaian pembangunan kota.')" />
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:title" content="@yield('title', 'Layanan Informasi RPJMD Kabupaten Pasuruan')">
  <meta property="og:description" content="@yield('meta_description', 'Portal Layanan Informasi RPJMD Kabupaten Pasuruan. Transparansi data, perencanaan, dan capaian pembangunan kota.')">
  <meta property="og:image" content="@yield('og_image', asset('hero.png'))">

  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="{{ url()->current() }}">
  <meta property="twitter:title" content="@yield('title', 'Layanan Informasi RPJMD Kabupaten Pasuruan')">
  <meta property="twitter:description" content="@yield('meta_description', 'Portal Layanan Informasi RPJMD Kabupaten Pasuruan. Transparansi data, perencanaan, dan capaian pembangunan kota.')">
  <meta property="twitter:image" content="@yield('og_image', asset('hero.png'))">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  
  <style>
    @keyframes kenBurns {
      0% { transform: scale(1); }
      100% { transform: scale(1.15); }
    }
    @keyframes textReveal {
      0% { opacity: 0; transform: translateY(30px); }
      100% { opacity: 1; transform: translateY(0); }
    }
    @keyframes chatSlideUp {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-chat-msg {
      animation: chatSlideUp 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    @keyframes shimmer {
      0% { background-position: -200% 0; }
      100% { background-position: 200% 0; }
    }
    .animate-ken-burns {
      animation: kenBurns 20s ease-in-out infinite alternate;
    }
    .hero-reveal {
      animation: textReveal 1.2s cubic-bezier(0.2, 1, 0.3, 1) forwards;
    }
    .btn-shimmer {
      background: linear-gradient(90deg, #2563eb, #3b82f6, #2563eb);
      background-size: 200% 100%;
      animation: shimmer 3s infinite linear;
    }
    .card-premium {
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-premium:hover {
      transform: translateY(-12px) scale(1.02);
      box-shadow: 0 25px 50px -12px rgba(37, 99, 235, 0.15);
    }
    .glass {
      background: rgba(255, 255, 255, 0.7);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
    }
    /* ===== PRELOADER ===== */
    #page-preloader {
      position: fixed; inset: 0; z-index: 9999;
      background: linear-gradient(135deg, #0a3d91 0%, #041a42 100%);
      display: flex; align-items: center; justify-content: center;
      flex-direction: column; gap: 20px;
      transition: opacity 0.6s ease, visibility 0.6s ease;
    }
    #page-preloader.fade-out {
      opacity: 0; visibility: hidden;
    }
    .preloader-logo {
      width: 72px; height: 72px;
      background: linear-gradient(135deg, #2563eb, #1d4ed8);
      border-radius: 20px;
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 0 40px rgba(59,130,246,0.5);
      animation: preloaderPulse 1.5s ease-in-out infinite;
    }
    .preloader-ring {
      width: 96px; height: 96px;
      border: 3px solid rgba(255,255,255,0.15);
      border-top-color: #facc15;
      border-radius: 50%;
      animation: spinRing 1s linear infinite;
      position: absolute;
    }
    @keyframes preloaderPulse {
      0%, 100% { transform: scale(1); box-shadow: 0 0 40px rgba(59,130,246,0.5); }
      50% { transform: scale(1.08); box-shadow: 0 0 60px rgba(59,130,246,0.8); }
    }
    @keyframes spinRing {
      to { transform: rotate(360deg); }
    }
    .preloader-text {
      color: rgba(255,255,255,0.9);
      font-family: 'Outfit', sans-serif;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      animation: preloaderFadeText 1.5s ease-in-out infinite;
    }
    @keyframes preloaderFadeText {
      0%, 100% { opacity: 0.5; }
      50% { opacity: 1; }
    }
    .chat-expanded {
      width: 800px !important;
      height: 80vh !important;
      max-width: 95vw !important;
      max-height: 900px !important;
      transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
  </style>
  @stack('styles')
</head>

<body class="font-sans text-gray-800 bg-white min-h-screen flex flex-col relative">

  <!-- ===== PRELOADER ===== -->
  <div id="page-preloader">
    <div class="relative flex items-center justify-center">
      <div class="preloader-ring"></div>
      <img src="{{ asset('logo.png') }}" class="w-16 h-16 object-contain" alt="Logo RPJMD" />
    </div>
    <div>
      <div class="preloader-text">Memuat Portal RPJMD</div>
      <div style="width:180px; height:2px; background:rgba(255,255,255,0.1); border-radius:99px; margin-top:10px; overflow:hidden;">
        <div id="preloader-bar" style="height:100%; width:0%; background:linear-gradient(90deg,#3b82f6,#facc15); border-radius:99px; transition: width 0.3s ease;"></div>
      </div>
    </div>
  </div>
  <script>
    // Start progress bar immediately
    let p = 0;
    const bar = document.getElementById('preloader-bar');
    const iv = setInterval(() => { p = Math.min(p + Math.random() * 15, 90); bar.style.width = p + '%'; }, 200);
    window.addEventListener('load', () => {
      clearInterval(iv);
      bar.style.width = '100%';
      setTimeout(() => {
        document.getElementById('page-preloader').classList.add('fade-out');
        setTimeout(() => { document.getElementById('page-preloader').remove(); }, 650);
      }, 300);
    });
  </script>

  <!-- Navbar -->
  <nav class="fixed w-full z-50 glass border-b border-gray-100 shadow-sm transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-20">
        <div class="flex items-center gap-3">
          <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ asset('logo.png') }}" class="w-10 h-10 object-contain" alt="Logo RPJMD" />
            <div>
              <div class="font-black text-xl tracking-tight text-blue-900 leading-tight">RPJMD</div>
              <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Kabupaten Pasuruan</div>
            </div>
          </a>
        </div>
        <div class="hidden lg:block">
          <div class="ml-10 flex items-baseline space-x-4">
            @if(request()->routeIs('berita.detail'))
              <a href="{{ route('home') }}" class="text-gray-600 font-medium hover:text-blue-600 px-2 py-2 transition-colors">Beranda</a>
              <a href="{{ route('berita') }}" class="text-blue-600 font-semibold border-b-2 border-blue-600 px-2 py-2 transition-colors">Informasi</a>
            @elseif(request()->routeIs('capaian'))
              <a href="{{ route('home') }}" class="text-gray-600 font-medium hover:text-blue-600 px-2 py-2 transition-colors">Beranda</a>
              <a href="{{ route('layanan') }}" class="text-gray-600 font-medium hover:text-blue-600 px-2 py-2 transition-colors">Layanan</a>
            @else
              <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-blue-600 font-semibold border-b-2 border-blue-600' : 'text-gray-600 font-medium hover:text-blue-600' }} px-2 py-2 transition-colors">Beranda</a>
              <a href="{{ route('profil') }}" class="{{ request()->routeIs('profil') ? 'text-blue-600 font-semibold border-b-2 border-blue-600' : 'text-gray-600 font-medium hover:text-blue-600' }} px-2 py-2 transition-colors">Profil</a>
              <a href="{{ route('berita') }}" class="{{ request()->routeIs('berita') ? 'text-blue-600 font-semibold border-b-2 border-blue-600' : 'text-gray-600 font-medium hover:text-blue-600' }} px-2 py-2 transition-colors">Informasi</a>
              <a href="{{ route('layanan') }}" class="{{ request()->routeIs('layanan') ? 'text-blue-600 font-semibold border-b-2 border-blue-600' : 'text-gray-600 font-medium hover:text-blue-600' }} px-2 py-2 transition-colors">Layanan</a>
              <a href="{{ route('kontak') }}" class="bg-blue-600 text-white px-4 py-2.5 rounded-full text-sm font-semibold hover:bg-blue-700 shadow-md hover:shadow-lg transition-all ml-2">Hubungi Kami</a>
            @endif
            
            @auth
              <a href="{{ route('admin.dashboard') }}" class="text-blue-600 font-bold px-2 py-2">Dashboard</a>
              <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-red-500 font-medium px-2 py-2">Logout</button>
              </form>
            @endauth
          </div>
        </div>

        <!-- Mobile/Tablet menu button (shown on < 1024px) -->
        <div class="lg:hidden flex items-center">
          <button id="mobile-menu-btn" class="p-2 rounded-md text-blue-900 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </button>
        </div>

      </div>
    </div>

    <!-- Mobile/Tablet Menu Panel (visible < 1024px) -->
    <div id="mobile-menu" class="hidden lg:hidden bg-white/95 backdrop-blur-md border-b border-gray-100 absolute w-full shadow-lg">
      <div class="px-4 pt-2 pb-6 space-y-1 sm:px-3 flex flex-col">
          <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} block px-3 py-3 rounded-md text-base font-bold transition-colors">Beranda</a>
          <a href="{{ route('profil') }}" class="{{ request()->routeIs('profil') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} block px-3 py-3 rounded-md text-base font-bold transition-colors">Profil</a>
          <a href="{{ route('berita') }}" class="{{ request()->routeIs('berita') || request()->routeIs('berita.detail') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} block px-3 py-3 rounded-md text-base font-bold transition-colors">Informasi & Berita</a>
          <a href="{{ route('layanan') }}" class="{{ request()->routeIs('layanan') || request()->routeIs('capaian') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} block px-3 py-3 rounded-md text-base font-bold transition-colors">Layanan Publik</a>
          <a href="{{ route('kontak') }}" class="mt-4 text-center bg-blue-600 text-white px-5 py-3 rounded-xl text-base font-bold hover:bg-blue-700 shadow-md">Hubungi Kami</a>
          
          @auth
            <div class="border-t border-gray-200 mt-4 pt-4"></div>
            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-3 rounded-md text-base font-black text-blue-700">Go to Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
              @csrf
              <button type="submit" class="w-full text-left px-3 py-3 rounded-md text-base font-bold text-red-600 hover:bg-red-50 transition-colors">Logout Account</button>
            </form>
          @endauth
      </div>
  </nav>

  <main class="flex-grow pt-20">
    @yield('content')
  </main>

  <!-- Footer -->
  <footer class="bg-[#041a42] text-gray-300 pt-16 pb-8 border-t border-blue-900 w-full mt-auto relative z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
        <div class="col-span-1 md:col-span-2">
          <div class="flex items-center gap-3 mb-6">
            <img src="{{ asset('logo.png') }}" class="w-8 h-8 object-contain" alt="Logo RPJMD" />
            <div class="font-black text-lg tracking-tight text-white leading-none">RPJMD Kabupaten Pasuruan</div>
          </div>
          <p class="text-gray-400 text-sm leading-relaxed max-w-sm mb-6 font-light">Portal Layanan Informasi Rencana Pembangunan Jangka Menengah Daerah (RPJMD) Kabupaten Pasuruan — menyajikan data perencanaan, capaian kinerja, dan informasi pembangunan kota secara transparan untuk masyarakat.</p>
          <div class="flex gap-4">
            <a href="{{ $socials['ig_link'] ?? '#' }}" target="_blank" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:bg-gradient-to-tr hover:from-purple-500 hover:to-pink-500 hover:text-white transition-all shadow-sm">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="{{ $socials['fb_link'] ?? '#' }}" target="_blank" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="https://wa.me/{{ $socials['wa_number'] ?? '' }}" target="_blank" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:bg-green-500 hover:text-white transition-all shadow-sm">
                <i class="fab fa-whatsapp"></i>
            </a>
          </div>
        </div>
        <div>
          <h4 class="text-white font-bold mb-4 tracking-wide">Tautan Cepat</h4>
          <ul class="space-y-3 text-sm text-gray-400 flex flex-col">
            <li><a href="{{ route('profil') }}" class="hover:text-blue-400 transition-colors">Profil Instansi</a></li>
            <li><a href="{{ route('layanan') }}" class="hover:text-blue-400 transition-colors">Layanan Publik</a></li>
            <li><a href="{{ route('berita') }}" class="hover:text-blue-400 transition-colors">Berita Terkini</a></li>
          </ul>
        </div>
        <div>
          <h4 class="text-white font-bold mb-4 tracking-wide">Kontak Kami</h4>
          <ul class="space-y-4 text-sm text-gray-400 flex flex-col">
            <li class="flex items-start group">
              <a href="{{ route('kontak') }}" class="hover:text-yellow-400 transition-colors">Kompleks Perkantoran Pemerintah Kabupaten Pasuruan
Gedung Berakhlak Lt. 2, Jl. Raya Raci Km. 09 Bangil – Pasuruan
</a>
            </li>
            <li class="flex items-center group">
              <a href="mailto:bapperida@pasuruankab.go.id" class="hover:text-yellow-400 transition-colors">bapperida@pasuruankab.go.id</a>
            </li>
          </ul>
        </div>
      </div>
      <div class="pt-8 border-t border-blue-900/50 flex flex-col md:flex-row justify-between items-center auto-cols-auto gap-4 text-sm text-gray-500 text-center md:text-left transition-all">
        <p>&copy; {{ date('Y') }} RPJMD Kabupaten Pasuruan. Hak Cipta Dilindungi.</p>
        <div class="flex gap-4">
          <a href="#" class="hover:text-blue-400 transition-colors">Kebijakan Privasi</a>
          <a href="#" class="hover:text-blue-400 transition-colors">Syarat & Ketentuan</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- AOS Script -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script>
    AOS.init({
      once: true,
      offset: 50,
      duration: 800,
      easing: 'ease-out-cubic',
    });

    // Mobile Menu Toggle
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    mobileBtn.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Terikirim!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2000,
            background: '#ffffff',
            color: '#1e293b',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Batal!',
            text: "{{ session('error') }}",
            confirmButtonColor: '#2563eb',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black'
            }
        });
    @endif
  </script>
  @stack('scripts')

  <!-- ========================================= -->
  <!-- FLOATING CHATBOT WIDGET (New Design)      -->
  <!-- ========================================= -->
  <script>
  document.addEventListener("DOMContentLoaded", () => {
    const chatbotHTML = `
      <div id="ai-chatbot-widget" class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50 flex flex-col items-end">
        
        <!-- Jendela Chat (Disembunyikan secara default) -->
        <div id="chat-window" class="hidden w-[calc(100vw-2rem)] sm:w-96 md:w-[400px] bg-white rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.2)] border border-gray-100 mb-3 sm:mb-4 overflow-hidden flex-col h-[calc(100vh-8rem)] sm:h-[500px] max-h-[600px] transform transition-all origin-bottom-right">
          
          <!-- Header Chat -->
          <div class="bg-gradient-to-r from-blue-700 to-[#041a42] p-4 sm:p-5 flex justify-between items-center text-white shrink-0">
            <div class="flex items-center gap-3">
              <div class="relative hidden sm:block">
                <div class="w-10 h-10 rounded-full ring-2 ring-yellow-400/50 bg-white/10 flex items-center justify-center overflow-hidden">
                  <svg class="w-6 h-6 text-yellow-300 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-blue-900 rounded-full animate-pulse"></span>
              </div>
              <div>
                <h4 class="font-bold text-sm sm:text-base tracking-wide flex items-center gap-2">
                  RPJMD Pasuruan AI 
                  <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                </h4>
                <p class="text-[10px] sm:text-xs text-blue-200 font-medium opacity-90">Asisten Virtual RPJMD</p>
              </div>
            </div>
            <div class="flex items-center gap-1">
              <button id="expand-chat" class="hidden sm:block text-blue-200 hover:text-white bg-white/5 hover:bg-white/20 p-2 rounded-full transition-colors cursor-pointer" title="Perbesar/Perkecil">
                <svg id="expand-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
              </button>
              <button id="close-chat" class="text-blue-200 hover:text-white bg-white/5 hover:bg-white/20 p-2 rounded-full transition-colors cursor-pointer" title="Tutup">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              </button>
            </div>
          </div>

          <!-- Tampilan Chat Messages -->
          <div id="chat-messages" class="flex-1 p-4 sm:p-5 bg-[#F8FAFC] overflow-y-auto flex flex-col gap-4" style="background-image: url('https://www.transparenttextures.com/patterns/absurdity.png')">
            
            <div class="flex justify-center mb-2">
              <span class="px-3 py-1 bg-gray-200/60 text-gray-500 rounded-full text-[10px] font-bold tracking-widest uppercase">Hari ini</span>
            </div>

            <!-- Robot Bubble -->
            <div class="flex justify-start group">
              <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mr-2 mt-1 hidden sm:flex">
                <span class="text-blue-600 font-bold text-xs">AI</span>
              </div>
              <div class="bg-white border border-gray-100 text-gray-800 rounded-2xl rounded-tl-sm px-4 sm:px-5 py-3 sm:py-3.5 max-w-[90%] sm:max-w-[80%] shadow-sm font-medium leading-relaxed text-[13px] sm:text-sm">
                Halo warga! 🙏 <br>Saya asisten AI Layanan Informasi RPJMD Kabupaten Pasuruan. Ada yang ingin Anda ketahui tentang program prioritas, capaian pembangunan, atau dokumen RPJMD 2025-2029?
              </div>
            </div>

            <!-- Quick Options -->
            <div id="chat-quick-options" class="flex flex-wrap gap-2 sm:ml-10 -mt-2 animate-chat-msg opacity-0" style="animation-delay: 0.3s forwards">
              <button class="quick-btn text-[10px] sm:text-[11px] font-bold bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-full transition-colors shadow-sm hover:shadow" data-text="Apa Visi dan Misi Kabupaten Pasuruan?">Visi Misi Kabupaten</button>
              <button class="quick-btn text-[10px] sm:text-[11px] font-bold bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-full transition-colors shadow-sm hover:shadow" data-text="Berapa jumlah Program Prioritas saat ini?">Program Prioritas</button>
              <button class="quick-btn text-[10px] sm:text-[11px] font-bold bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-full transition-colors shadow-sm hover:shadow" data-text="Tolong jelaskan apa itu RPJMD secara singkat.">Apa itu RPJMD?</button>
            </div>

          </div>

          <!-- Area Input Chat -->
          <div class="p-3 sm:p-4 bg-white border-t border-gray-100 flex items-center gap-2 sm:gap-3 shrink-0">
            <button class="hidden sm:block p-2 text-gray-400 hover:text-blue-600 transition-colors bg-gray-50 rounded-full shrink-0">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
            </button>
            <input id="chat-input" type="text" placeholder="Tanyakan seputar RPJMD..." class="flex-1 bg-gray-50 border border-gray-200 rounded-full px-4 sm:px-5 py-2 sm:py-2.5 text-base sm:text-sm focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all font-medium">
            <button id="chat-send" class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 shadow-md hover:shadow-lg transition-transform hover:-translate-y-0.5 shrink-0 focus:outline-none">
              <svg class="w-4 h-4 sm:w-5 sm:h-5 translate-x-[-1px] translate-y-[1px]" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path></svg>
            </button>
          </div>

        </div>

        <!-- Tombol Buka Tutup Chat (Widget Indikator) -->
        <button id="chat-toggle" class="group relative w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-800 rounded-full shadow-[0_10px_25px_rgba(37,99,235,0.5)] border-[3px] sm:border-4 border-white/20 flex items-center justify-center text-white hover:scale-110 transition-all duration-300 focus:outline-none cursor-pointer mt-2 sm:mt-4">
          <svg class="w-7 h-7 sm:w-8 sm:h-8 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
          <!-- Red dot counter alert -->
          <span id="chat-notif-dot" class="absolute top-0 right-0 w-3.5 h-3.5 sm:w-4 sm:h-4 bg-red-500 border-2 border-white rounded-full flex items-center justify-center animate-bounce"></span>
        </button>
        
      </div>
    `;

    // Menanamkan elemen HTML chatbot ini langsung ke ujung tag <body>
    document.body.insertAdjacentHTML('beforeend', chatbotHTML);

    // Logika Fungsional Vanilla JS Buka/Tutup
    const chatToggle = document.getElementById('chat-toggle');
    const chatWindow = document.getElementById('chat-window');
    const closeChat  = document.getElementById('close-chat');
    const expandChat = document.getElementById('expand-chat');
    const expandIcon = document.getElementById('expand-icon');
    const chatInput  = document.getElementById('chat-input');
    const chatSend   = document.getElementById('chat-send');
    const notifDot   = document.getElementById('chat-notif-dot');
    const messages   = document.getElementById('chat-messages');

    function handleToggle() {
      if (chatWindow.classList.contains('hidden')) {
        // Buka
        chatWindow.classList.remove('hidden');
        chatWindow.classList.add('flex');
        chatWindow.animate([
          { opacity: 0, transform: 'scale(0.8) translateY(20px)' },
          { opacity: 1, transform: 'scale(1) translateY(0)' }
        ], { duration: 300, easing: 'cubic-bezier(0.175, 0.885, 0.32, 1.275)', fill: 'forwards' });
        chatToggle.classList.add('scale-75', 'opacity-60');
        if (notifDot) notifDot.classList.add('hidden');
        setTimeout(() => chatInput?.focus(), 350);
      } else {
        // Tutup
        chatWindow.animate([
          { opacity: 1, transform: 'scale(1) translateY(0)' },
          { opacity: 0, transform: 'scale(0.8) translateY(20px)' }
        ], { duration: 200, fill: 'forwards' }).onfinish = () => {
          chatWindow.classList.add('hidden');
          chatWindow.classList.remove('flex');
        };
        chatToggle.classList.remove('scale-75', 'opacity-60');
      }
    }

    chatToggle.addEventListener('click', handleToggle);
    closeChat.addEventListener('click', handleToggle);

    expandChat.addEventListener('click', () => {
      if (chatWindow.classList.contains('chat-expanded')) {
        chatWindow.classList.remove('chat-expanded');
        expandIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>';
      } else {
        chatWindow.classList.add('chat-expanded');
        expandIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5v4h-4 M4 9l5-5 M15 5v4h4 M20 9l-5-5 M9 19v-4h-4 M4 15l5 5 M15 19v-4h4 M20 15l-5 5"></path>';
      }
    });

    // Kirim pesan
    function sendMessage() {
      const msg = chatInput.value.trim();
      if (!msg) return;

      // Matikan Input (Disable) agar user tidak bisa mengetik sebelum AI menjawab
      chatInput.disabled = true;
      chatSend.disabled = true;
      chatSend.classList.add('opacity-50', 'cursor-not-allowed');
      chatInput.placeholder = "Menunggu balasan AI...";

      // Sembunyikan quick options setelah pesan pertama terkirim
      const quickOptions = document.getElementById('chat-quick-options');
      if (quickOptions) quickOptions.style.display = 'none';

      // Bubble user
      messages.innerHTML += `
        <div class="flex justify-end animate-chat-msg opacity-0">
          <div class="bg-blue-600 text-white rounded-2xl rounded-tr-sm px-5 py-3 max-w-[80%] shadow-sm font-medium leading-relaxed text-sm">${msg}</div>
        </div>`;
      chatInput.value = '';
      messages.scrollTop = messages.scrollHeight;

      // Typing indicator
      const typingId = 'typing-' + Date.now();
      messages.innerHTML += `
        <div id="${typingId}" class="flex justify-start animate-chat-msg opacity-0">
          <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mr-2 mt-1">
            <span class="text-blue-600 font-bold text-xs">AI</span>
          </div>
          <div class="bg-white border border-gray-100 text-gray-800 rounded-2xl rounded-tl-sm px-5 py-4 shadow-sm flex items-center gap-1.5">
            <div class="w-2.5 h-2.5 bg-blue-400 rounded-full animate-bounce"></div>
            <div class="w-2.5 h-2.5 bg-blue-500 rounded-full animate-bounce" style="animation-delay:0.15s"></div>
            <div class="w-2.5 h-2.5 bg-blue-600 rounded-full animate-bounce" style="animation-delay:0.3s"></div>
          </div>
        </div>`;
      messages.scrollTop = messages.scrollHeight;

      // Fungsi Pembuka Gembok
      function unlockChat() {
          chatInput.disabled = false;
          chatSend.disabled = false;
          chatSend.classList.remove('opacity-50', 'cursor-not-allowed');
          chatInput.placeholder = "Tanyakan seputar RPJMD Kabupaten Pasuruan...";
          setTimeout(() => chatInput.focus(), 100);
      }

      // Fetch reply from backend RAG API
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      // Hardcode Quick Options untuk hemat kuota API
      const quickResponses = {
        "Apa Visi dan Misi Kabupaten Pasuruan?": "Pemerintah Kabupaten Pasuruan memiliki **Visi**:<br>\"Mewujudkan Kabupaten Pasuruan Kota Madinah (Maju Ekonominya, Indah Kotanya, Harmoni Warganya).\"<br><br>**Misi Utama**:<br>1. Mempercepat pertumbuhan ekonomi<br>2. Meningkatkan tata kelola pemerintahan yang baik<br>3. Pemerataan pembangunan infrastruktur<br>4. Membangun SDM unggul dan berdaya saing.",
        "Berapa jumlah Program Prioritas saat ini?": "Berdasarkan data RPJMD terbaru, Pemerintah Kabupaten Pasuruan saat ini menargetkan lebih dari **78+ Program Prioritas** yang dieksekusi secara berkesinambungan di berbagai sektor (Infrastruktur, Pelayanan Publik, Pendidikan, dll) selama periode 5 tahun ke depan.",
        "Tolong jelaskan apa itu RPJMD secara singkat.": "**RPJMD** (Rencana Pembangunan Jangka Menengah Daerah) adalah pedoman perencanaan resmi daerah untuk periode 5 tahun. Dokumen ini menjabarkan arah kebijakan, visi, misi, dan program kerja Kepala Daerah yang dijaga transparansinya untuk publik."
      };

      if (quickResponses[msg]) {
        setTimeout(() => {
          document.getElementById(typingId)?.remove();
          messages.innerHTML += `
            <div class="flex justify-start animate-chat-msg opacity-0">
              <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mr-2 mt-1">
                <span class="text-blue-600 font-bold text-xs">AI</span>
              </div>
              <div class="bg-white border border-gray-100 text-gray-800 rounded-2xl rounded-tl-sm px-5 py-3.5 max-w-[80%] shadow-sm font-medium leading-relaxed text-sm">${quickResponses[msg]}</div>
            </div>`;
          messages.scrollTop = messages.scrollHeight;
          unlockChat(); // Buka gembok
        }, 1000); // Simulasi delay ngetik 1 detik
        return; // Jangan fetch API
      }
      
      fetch('/api/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ message: msg })
      })
      .then(response => response.json())
      .then(data => {
        document.getElementById(typingId)?.remove();
        
        const reply = data.reply || 'Maaf, terjadi kesalahan saat menghubungi AI.';
        
        // Use marked.js or basic replace to handle newlines/bold from Gemini
        const formattedReply = reply.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

        messages.innerHTML += `
          <div class="flex justify-start animate-chat-msg opacity-0">
            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mr-2 mt-1">
              <span class="text-blue-600 font-bold text-xs">AI</span>
            </div>
            <div class="bg-white border border-gray-100 text-gray-800 rounded-2xl rounded-tl-sm px-5 py-3.5 max-w-[80%] shadow-sm font-medium leading-relaxed text-sm">${formattedReply}</div>
          </div>`;
        messages.scrollTop = messages.scrollHeight;
        unlockChat(); // Buka gembok
      })
      .catch(error => {
        document.getElementById(typingId)?.remove();
        messages.innerHTML += `
          <div class="flex justify-start animate-chat-msg opacity-0">
            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0 mr-2 mt-1">
              <span class="text-red-500 font-bold text-xs">AI</span>
            </div>
            <div class="bg-white border border-red-100 text-red-600 rounded-2xl rounded-tl-sm px-5 py-3.5 max-w-[80%] shadow-sm font-medium leading-relaxed text-sm">Gagal terhubung dengan server. Silakan coba lagi.</div>
          </div>`;
        messages.scrollTop = messages.scrollHeight;
        unlockChat(); // Buka gembok
      });
    }

    chatSend.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') sendMessage();
    });

    // Quick Options Logic
    document.querySelectorAll('.quick-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        chatInput.value = this.getAttribute('data-text');
        sendMessage();
      });
    });
  });
  </script>
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js').then(function(registration) {
          console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function(err) {
          console.log('ServiceWorker registration failed: ', err);
        });
      });
    }
  </script>
</body>
</html>
