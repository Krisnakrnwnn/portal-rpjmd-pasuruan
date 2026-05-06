<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Performance & SEO Components --}}
  <x-head-performance />
  @yield('seo')
  @hasSection('seo')
  @else
    <x-seo />
  @endif

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
    
    /* ===== CHATBOT IMPROVEMENTS ===== */
    /* Typing indicator animation */
    .typing-indicator {
      display: flex;
      gap: 4px;
      padding: 12px 16px;
    }
    .typing-indicator span {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #3b82f6;
      animation: typing 1.4s infinite;
    }
    .typing-indicator span:nth-child(2) { 
      animation-delay: 0.2s; 
    }
    .typing-indicator span:nth-child(3) { 
      animation-delay: 0.4s; 
    }
    @keyframes typing {
      0%, 60%, 100% { 
        transform: translateY(0); 
        opacity: 0.5; 
      }
      30% { 
        transform: translateY(-10px); 
        opacity: 1; 
      }
    }
    
    /* Styling untuk markdown content di chatbot */
    #chat-messages .prose {
      color: #374151;
      font-size: 13px;
    }
    #chat-messages .prose strong {
      color: #1f2937;
      font-weight: 700;
    }
    #chat-messages .prose ul, 
    #chat-messages .prose ol {
      margin: 0.5rem 0;
      padding-left: 1.5rem;
    }
    #chat-messages .prose li {
      margin: 0.25rem 0;
    }
    #chat-messages .prose p {
      margin: 0.5rem 0;
    }
    #chat-messages .prose h1, 
    #chat-messages .prose h2, 
    #chat-messages .prose h3 {
      font-weight: 700;
      margin-top: 1rem;
      margin-bottom: 0.5rem;
      color: #1f2937;
    }
    #chat-messages .prose code {
      background: #f3f4f6;
      padding: 2px 6px;
      border-radius: 4px;
      font-size: 12px;
    }
    
    /* Table formatting for structured data */
    #chat-messages .prose table {
      width: 100%;
      border-collapse: collapse;
      margin: 0.5rem 0;
      font-size: 11px;
    }
    #chat-messages .prose table th,
    #chat-messages .prose table td {
      border: 1px solid #e5e7eb;
      padding: 4px 6px;
      text-align: left;
    }
    #chat-messages .prose table th {
      background: #f3f4f6;
      font-weight: 600;
    }
    
    /* Pre-formatted text for data tables */
    #chat-messages .prose pre {
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
      padding: 8px;
      overflow-x: auto;
      font-size: 10px;
      line-height: 1.4;
      font-family: 'Courier New', monospace;
      white-space: pre;
    }
    
    /* Better line breaks and spacing */
    #chat-messages .bg-white,
    #chat-messages .prose {
      word-wrap: break-word;
      overflow-wrap: break-word;
    }
    
    /* Horizontal scroll for wide content */
    #chat-messages .prose {
      max-width: 100%;
      overflow-x: auto;
    }
    
    /* Better formatting for numbers and data */
    #chat-messages .prose {
      font-variant-numeric: tabular-nums;
    }
    
    /* Compact spacing for data-heavy responses */
    #chat-messages .prose.data-heavy {
      font-size: 11px;
      line-height: 1.5;
    }
    
    /* Feedback button active state */
    .feedback-active {
      opacity: 1 !important;
    }
    
    /* Voice input recording animation */
    .voice-recording {
      animation: voicePulse 1.5s ease-in-out infinite;
    }
    @keyframes voicePulse {
      0%, 100% { 
        transform: scale(1); 
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
      }
      50% { 
        transform: scale(1.1); 
        box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
      }
    }
    
    /* Voice output playing animation */
    .voice-playing {
      animation: voiceWave 1s ease-in-out infinite;
    }
    @keyframes voiceWave {
      0%, 100% { opacity: 0.5; }
      50% { opacity: 1; }
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
      <div class="preloader-text">Memuat Portal Bapperida</div>
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
            <img src="{{ asset('logo.png') }}" class="w-10 h-10 object-contain" alt="Logo Bapperida" />
            <div>
              <div class="font-black text-xl tracking-tight text-blue-900 leading-tight">BAPPERIDA</div>
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
            <img src="{{ asset('logo.png') }}" class="w-8 h-8 object-contain" alt="Logo Bapperida" />
            <div class="font-black text-lg tracking-tight text-white leading-none">Bapperida Kabupaten Pasuruan</div>
          </div>
          <p class="text-gray-400 text-sm leading-relaxed max-w-sm mb-6 font-light">Portal Layanan Informasi Badan Perencanaan Pembangunan, Riset, dan Inovasi Daerah (Bapperida) Kabupaten Pasuruan — menyajikan data perencanaan, capaian kinerja, dan inovasi pembangunan daerah secara transparan.</p>
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

  <!-- Marked.js untuk Markdown Rendering -->
  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <script>
    // Configure marked untuk format yang lebih baik
    if (typeof marked !== 'undefined') {
      marked.setOptions({
        breaks: true,        // Line breaks jadi <br>
        gfm: true,          // GitHub Flavored Markdown
        headerIds: false,   // Tidak perlu ID di header
        mangle: false       // Jangan encode email
      });
    }
  </script>

  <!-- ========================================= -->
  <!-- FLOATING CHATBOT WIDGET (Improved)        -->
  <!-- ========================================= -->
  <script>
  // Function to get time-based greeting
  function getTimeBasedGreeting() {
    const hour = new Date().getHours();
    if (hour >= 0 && hour < 11) return 'Selamat pagi';
    if (hour >= 11 && hour < 15) return 'Selamat siang';
    if (hour >= 15 && hour < 18) return 'Selamat sore';
    return 'Selamat malam';
  }
  
  document.addEventListener("DOMContentLoaded", () => {
    const greeting = getTimeBasedGreeting();
    const chatbotHTML = `
      <div id="ai-chatbot-widget" class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50 flex flex-col items-end">
        
        <!-- Jendela Chat (Disembunyikan secara default) -->
        <div id="chat-window" class="hidden w-[calc(100vw-2rem)] sm:w-96 md:w-[400px] bg-white rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.2)] border border-gray-100 mb-3 sm:mb-4 overflow-hidden flex-col h-[calc(100vh-8rem)] sm:h-[500px] max-h-[600px] transform transition-all origin-bottom-right">
          
          <!-- Header Chat -->
          <div class="bg-gradient-to-r from-blue-700 to-[#041a42] p-4 sm:p-5 flex justify-between items-center text-white shrink-0 shadow-xl relative z-10">
            <div class="flex items-center gap-3">
              <div class="relative">
                <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 p-2 flex items-center justify-center overflow-hidden shadow-inner">
                  <img src="{{ asset('logo.png') }}" class="w-full h-full object-contain" alt="Logo">
                </div>
                <!-- Status Dot: Now strictly absolute to the logo container -->
                <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 border-2 border-[#041a42] rounded-full shadow-lg translate-x-1/4 translate-y-1/4"></span>
              </div>
              <div class="flex flex-col">
                <div class="flex items-center gap-1.5">
                  <h4 class="font-black text-sm sm:text-base tracking-tight leading-tight">
                    RPJMD Pasuruan AI
                  </h4>
                  <svg class="w-3.5 h-3.5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-medium text-blue-200/80 tracking-wide">Asisten Virtual RPJMD</span>
              </div>
            </div>
            <div class="flex items-center gap-1">
              <button id="expand-chat" class="hidden sm:block text-white/70 hover:text-white hover:bg-white/10 p-2 rounded-xl transition-all cursor-pointer" title="Perbesar/Perkecil">
                <svg id="expand-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
              </button>
              <button id="close-chat" class="text-white/70 hover:text-white hover:bg-white/10 p-2 rounded-xl transition-all cursor-pointer" title="Tutup">
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
              <div class="bg-white border border-gray-100 text-gray-800 rounded-2xl rounded-tl-sm px-4 sm:px-5 py-3 sm:py-3.5 max-w-[95%] sm:max-w-[85%] shadow-sm font-medium leading-relaxed text-[13px] sm:text-sm">
                ${greeting}! 🙏 <br>Saya asisten AI Layanan Informasi RPJMD Kabupaten Pasuruan. Ada yang ingin Anda ketahui tentang program prioritas, capaian pembangunan, atau dokumen RPJMD 2025-2029?
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
            <button id="voice-input-btn" class="hidden sm:block p-2 text-gray-400 hover:text-blue-600 transition-colors bg-gray-50 rounded-full shrink-0" title="Input Suara">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
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
    const clearHistory = document.getElementById('clear-history');
    let isChatBusy = false; // Flag untuk mengunci chat
    let messageCount = 0; // Counter untuk pesan
    // ===== VOICE INPUT/OUTPUT CLASSES =====
    class VoiceInput {
      constructor() {
        this.recognition = null;
        this.isSupported = 'webkitSpeechRecognition' in window || 'SpeechRecognition' in window;
        if (this.isSupported) {
          const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
          this.recognition = new SpeechRecognition();
          this.recognition.lang = 'id-ID';
          this.recognition.continuous = false;
          this.recognition.interimResults = false;
        }
      }
      start() {
        if (!this.isSupported || !this.recognition) return Promise.reject('Not supported');
        return new Promise((resolve, reject) => {
          this.recognition.onresult = (e) => resolve(e.results[0][0].transcript);
          this.recognition.onerror = (e) => reject(e.error);
          this.recognition.start();
        });
      }
      stop() { this.recognition && this.recognition.stop(); }
    }

    class VoiceOutput {
      constructor() {
        this.synthesis = window.speechSynthesis;
        this.isSupported = 'speechSynthesis' in window;
      }
      speak(text) {
        if (!this.isSupported) return Promise.reject('Not supported');
        return new Promise((resolve, reject) => {
          this.synthesis.cancel();
          const cleanText = text.replace(/<[^>]*>/g, '').replace(/\*\*(.*?)\*\*/g, '$1').replace(/\n/g, '. ');
          const utterance = new SpeechSynthesisUtterance(cleanText);
          utterance.lang = 'id-ID';
          utterance.onend = () => resolve();
          utterance.onerror = (e) => reject(e);
          this.synthesis.speak(utterance);
        });
      }
      stop() { this.synthesis && this.synthesis.cancel(); }
    }

    const voiceInput = new VoiceInput();
    const voiceOutput = new VoiceOutput();
    let isRecording = false;

    // ===== SIMPLIFIED LANGUAGE MANAGER (Indonesian Only) =====
    const translations = {
      id: {
        placeholder: 'Tanyakan seputar RPJMD...',
        clear: 'Hapus Riwayat',
        export: 'Ekspor Chat',
        greeting: (time) => `${time}! 🙏 <br>Saya asisten AI Layanan Informasi RPJMD Kabupaten Pasuruan. Ada yang ingin Anda ketahui tentang program prioritas, capaian pembangunan, atau dokumen RPJMD 2025-2029?`,
        today: 'Hari ini',
        noConversation: 'Tidak Ada Percakapan',
        noConversationText: 'Belum ada percakapan untuk diekspor. Mulai chat terlebih dahulu!',
        exportTitle: '📥 Ekspor Percakapan',
        exportText: (count) => `Pilih format file untuk mengekspor ${count} pesan:`,
        processing: 'Memproses...',
        processingText: (format) => `Sedang membuat file ${format.toUpperCase()}...`,
        success: 'Berhasil!',
        exportSuccess: (format) => `File ${format.toUpperCase()} berhasil diunduh`,
        exportFailed: 'Gagal Mengekspor',
        exportFailedText: 'Terjadi kesalahan saat mengekspor chat.',
        confirmClear: 'Yakin ingin menghapus semua riwayat percakapan?',
        voiceInputError: 'Gagal Merekam',
        voiceInputErrorText: 'Pastikan izin mikrofon sudah diberikan.'
      }
    };

    const languageManager = {
      currentLang: 'id',
      translate: (key, ...args) => {
        const val = translations.id[key];
        return typeof val === 'function' ? val(...args) : val;
      },
      reloadGreeting: () => {
        const time = getTimeBasedGreeting();
        const msg = languageManager.translate('greeting', time);
        messages.innerHTML = `
          <div class="flex justify-center mb-2">
            <span class="px-3 py-1 bg-gray-200/60 text-gray-500 rounded-full text-[10px] font-bold tracking-widest uppercase">${languageManager.translate('today')}</span>
          </div>
          <div class="flex justify-start group">
            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mr-2 mt-1 hidden sm:flex">
              <span class="text-blue-600 font-bold text-xs">AI</span>
            </div>
            <div class="bg-white border border-gray-100 text-gray-800 rounded-2xl rounded-tl-sm px-4 sm:px-5 py-3 sm:py-3.5 max-w-[95%] sm:max-w-[85%] shadow-sm font-medium leading-relaxed text-[13px] sm:text-sm">
              ${msg}
            </div>
          </div>
          <div id="chat-quick-options" class="flex flex-wrap gap-2 sm:ml-10 -mt-2 animate-chat-msg opacity-0" style="animation-delay: 0.3s forwards">
            <button class="quick-btn text-[10px] sm:text-[11px] font-bold bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-full transition-colors shadow-sm hover:shadow" data-text="Apa Visi dan Misi Kabupaten Pasuruan?">Visi Misi</button>
            <button class="quick-btn text-[10px] sm:text-[11px] font-bold bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-full transition-colors shadow-sm hover:shadow" data-text="Berapa jumlah Program Prioritas saat ini?">Program Prioritas</button>
            <button class="quick-btn text-[10px] sm:text-[11px] font-bold bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-full transition-colors shadow-sm hover:shadow" data-text="Tolong jelaskan apa itu RPJMD secara singkat.">Apa itu RPJMD?</button>
          </div>
        `;
        document.querySelectorAll('.quick-btn').forEach(btn => {
          btn.addEventListener('click', function() {
            if (isChatBusy) return;
            chatInput.value = this.getAttribute('data-text');
            sendMessage();
          });
        });
      }
    };

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
        
        // Load history from database when opening
        loadChatHistory();
        
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
    
    // Load chat history from database
    async function loadChatHistory() {
      try {
        const response = await fetch('/api/chat/history');
        const data = await response.json();
        
        if (data.messages && data.messages.length > 0) {
          // Clear current messages except the greeting
          const quickOptions = document.getElementById('chat-quick-options');
          if (quickOptions) quickOptions.style.display = 'none';
          
          // Add loaded messages
          data.messages.forEach(msg => {
            if (msg.role === 'user') {
              messages.innerHTML += `
                <div class="flex justify-end animate-chat-msg opacity-0">
                  <div class="bg-blue-600 text-white rounded-2xl rounded-tr-sm px-5 py-3 max-w-[80%] shadow-sm font-medium leading-relaxed text-sm">${msg.text}</div>
                </div>`;
            } else {
              messageCount++;
              const msgId = 'msg-' + messageCount;
              const formattedReply = typeof marked !== 'undefined' ? marked.parse(msg.message) : msg.message.replace(/\n/g, '<br>');
              
              messages.innerHTML += `
                <div class="flex justify-start animate-chat-msg opacity-0 group" id="${msgId}">
                  <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mr-2 mt-1 hidden sm:flex">
                    <span class="text-blue-600 font-bold text-xs">AI</span>
                  </div>
                  <div class="flex-1 max-w-[95%] sm:max-w-[85%]">
                    <div class="bg-white border border-gray-100 text-gray-800 rounded-2xl rounded-tl-sm px-4 sm:px-5 py-3 sm:py-3.5 shadow-sm leading-relaxed text-[13px] sm:text-sm prose prose-sm max-w-none">${formattedReply}</div>
                    <div class="flex items-center gap-2 mt-1 ml-2 opacity-0 group-hover:opacity-100 transition-opacity">
                      <button onclick="speakMessage('${msgId}')" class="text-gray-400 hover:text-blue-600 p-1 rounded transition-colors" title="Dengarkan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                      </button>
                      <button onclick="copyMessage('${msgId}')" class="text-gray-400 hover:text-blue-600 p-1 rounded transition-colors" title="Salin">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                      </button>
                      <button onclick="feedbackMessage('${msgId}', 'like')" class="text-gray-400 hover:text-green-600 p-1 rounded transition-colors" title="Berguna">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
                      </button>
                      <button onclick="feedbackMessage('${msgId}', 'dislike')" class="text-gray-400 hover:text-red-600 p-1 rounded transition-colors" title="Kurang Berguna">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path></svg>
                      </button>
                    </div>
                  </div>
                </div>`;
            }
          });
          
          messages.scrollTop = messages.scrollHeight;
        }
      } catch (error) {
        console.error('Failed to load history:', error);
        // Silent fail, show default greeting
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

    // Kirim pesan (IMPROVED)
    function sendMessage() {
      if (isChatBusy) return;
      
      const msg = chatInput.value.trim();
      if (!msg) return;

      isChatBusy = true;
      chatInput.disabled = true;
      chatSend.disabled = true;
      chatSend.classList.add('opacity-50', 'cursor-not-allowed');
      chatInput.placeholder = "Menunggu balasan AI...";

      // Sembunyikan quick options
      const quickOptions = document.getElementById('chat-quick-options');
      if (quickOptions) quickOptions.style.display = 'none';

      // Bubble user
      messages.innerHTML += `
        <div class="flex justify-end animate-chat-msg opacity-0">
          <div class="bg-blue-600 text-white rounded-2xl rounded-tr-sm px-5 py-3 max-w-[80%] shadow-sm font-medium leading-relaxed text-sm">${msg}</div>
        </div>`;
      chatInput.value = '';
      messages.scrollTop = messages.scrollHeight;

      // Typing indicator (IMPROVED)
      const typingId = 'typing-' + Date.now();
      messages.innerHTML += `
        <div id="${typingId}" class="flex justify-start">
          <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mr-2 mt-1 hidden sm:flex">
            <span class="text-blue-600 font-bold text-xs">AI</span>
          </div>
          <div class="bg-white border border-gray-100 rounded-2xl rounded-tl-sm shadow-sm">
            <div class="typing-indicator">
              <span></span><span></span><span></span>
            </div>
          </div>
        </div>`;
      messages.scrollTop = messages.scrollHeight;

      // Fungsi Pembuka Gembok
      function unlockChat() {
          isChatBusy = false;
          chatInput.disabled = false;
          chatSend.disabled = false;
          chatSend.classList.remove('opacity-50', 'cursor-not-allowed');
          chatInput.placeholder = "Tanyakan seputar RPJMD...";
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
        body: JSON.stringify({ 
          message: msg,
          language: languageManager.currentLang
        })
      })
      .then(response => response.json())
      .then(data => {
        document.getElementById(typingId)?.remove();
        
        const reply = data.reply || 'Maaf, terjadi kesalahan saat menghubungi AI.';
        
        // IMPROVED: Use marked.js for proper markdown rendering
        const formattedReply = typeof marked !== 'undefined' ? marked.parse(reply) : reply.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        
        messageCount++;
        const msgId = 'msg-' + messageCount;

        messages.innerHTML += `
          <div class="flex justify-start animate-chat-msg opacity-0 group" id="${msgId}">
            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mr-2 mt-1 hidden sm:flex">
              <span class="text-blue-600 font-bold text-xs">AI</span>
            </div>
            <div class="flex-1 max-w-[95%] sm:max-w-[85%]">
              <div class="bg-white border border-gray-100 text-gray-800 rounded-2xl rounded-tl-sm px-4 sm:px-5 py-3 sm:py-3.5 shadow-sm leading-relaxed text-[13px] sm:text-sm prose prose-sm max-w-none">${formattedReply}</div>
              <div class="flex items-center gap-2 mt-1 ml-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button onclick="speakMessage('${msgId}')" class="text-gray-400 hover:text-blue-600 p-1 rounded transition-colors" title="Dengarkan">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                </button>
                <button onclick="copyMessage('${msgId}')" class="text-gray-400 hover:text-blue-600 p-1 rounded transition-colors" title="Salin">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                </button>
                <button onclick="feedbackMessage('${msgId}', 'like')" class="text-gray-400 hover:text-green-600 p-1 rounded transition-colors" title="Berguna">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
                </button>
                <button onclick="feedbackMessage('${msgId}', 'dislike')" class="text-gray-400 hover:text-red-600 p-1 rounded transition-colors" title="Kurang Berguna">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path></svg>
                </button>
              </div>
            </div>
          </div>`;
        messages.scrollTop = messages.scrollHeight;
        unlockChat();
      })
      .catch(error => {
        document.getElementById(typingId)?.remove();
        messages.innerHTML += `
          <div class="flex justify-start animate-chat-msg opacity-0">
            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0 mr-2 mt-1 hidden sm:flex">
              <span class="text-red-500 font-bold text-xs">⚠️</span>
            </div>
            <div class="bg-red-50 border border-red-100 text-red-600 rounded-2xl rounded-tl-sm px-5 py-3.5 max-w-[80%] shadow-sm font-medium leading-relaxed text-sm">❌ Gagal terhubung dengan server. Silakan coba lagi.</div>
          </div>`;
        messages.scrollTop = messages.scrollHeight;
        unlockChat();
      });
    }

    chatSend.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') sendMessage();
    });

    // Quick Options Logic
    document.querySelectorAll('.quick-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        if (isChatBusy) return; // Jangan jalankan jika sibuk
        chatInput.value = this.getAttribute('data-text');
        sendMessage();
      });
    });

    // Export Chat Logic
    const exportChat = document.getElementById('export-chat');
    exportChat.addEventListener('click', async function() {
      if (isChatBusy) return;
      
      // Get all messages from chat
      const chatMessages = [];
      const messageDivs = messages.querySelectorAll('.flex.justify-start, .flex.justify-end');
      
      messageDivs.forEach(div => {
        const isUser = div.classList.contains('justify-end');
        const textElement = div.querySelector('.prose, .bg-white, .bg-blue-600');
        if (textElement) {
          chatMessages.push({
            role: isUser ? 'user' : 'ai',
            text: textElement.innerText || textElement.textContent
          });
        }
      });
      
      if (chatMessages.length === 0) {
        Swal.fire({
          icon: 'warning',
          title: languageManager.translate('noConversation'),
          text: languageManager.translate('noConversationText'),
          confirmButtonColor: '#2563eb',
          customClass: {
            popup: 'rounded-3xl border-none shadow-2xl',
            title: 'font-black'
          }
        });
        return;
      }
      
      // Show format selection with SweetAlert2
      const { value: format } = await Swal.fire({
        title: languageManager.translate('exportTitle'),
        html: `
          <div class="text-left space-y-3 mt-4">
            <p class="text-gray-600 text-sm mb-4">${languageManager.translate('exportText', chatMessages.length)}</p>
          </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-file-pdf mr-2"></i> PDF',
        cancelButtonText: '<i class="fas fa-file-alt mr-2"></i> TXT',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#2563eb',
        reverseButtons: true,
        customClass: {
          popup: 'rounded-3xl border-none shadow-2xl',
          title: 'font-black text-xl',
          confirmButton: 'rounded-xl px-6 py-3 font-bold',
          cancelButton: 'rounded-xl px-6 py-3 font-bold'
        }
      });
      
      // User clicked X or Esc
      if (format === undefined) return;
      
      const selectedFormat = format ? 'pdf' : 'txt';
      
      // Show loading
      Swal.fire({
        title: languageManager.translate('processing'),
        html: languageManager.translate('processingText', selectedFormat),
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
          Swal.showLoading();
        },
        customClass: {
          popup: 'rounded-3xl border-none shadow-2xl',
          title: 'font-black'
        }
      });
      
      // Send export request
      fetch('/api/chat/export', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify({
          messages: chatMessages,
          format: selectedFormat
        })
      })
      .then(response => {
        if (!response.ok) throw new Error('Export failed');
        return response.blob();
      })
      .then(blob => {
        // Trigger download
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `chat-rpjmd-${Date.now()}.${selectedFormat}`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        // Show success
        Swal.fire({
          icon: 'success',
          title: languageManager.translate('success'),
          text: languageManager.translate('exportSuccess', selectedFormat),
          timer: 2000,
          showConfirmButton: false,
          customClass: {
            popup: 'rounded-3xl border-none shadow-2xl',
            title: 'font-black'
          }
        });
      })
      .catch(error => {
        console.error('Export error:', error);
        Swal.fire({
          icon: 'error',
          title: languageManager.translate('exportFailed'),
          text: languageManager.translate('exportFailedText'),
          confirmButtonColor: '#2563eb',
          customClass: {
            popup: 'rounded-3xl border-none shadow-2xl',
            title: 'font-black'
          }
        });
      });
    });

    // Clear History Logic
    clearHistory.addEventListener('click', function() {
      if (isChatBusy) return;
      
      if (confirm(languageManager.translate('confirmClear'))) {
        // Reload greeting with current language
        languageManager.reloadGreeting();
        
        // Clear server-side session
        fetch('/api/chat/clear', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
          }
        }).catch(() => {
          // Silent fail, UI already cleared
        });
        
        messageCount = 0;
      }
    });
    
    // New Session Logic
    const newSessionBtn = document.getElementById('new-session');
    if (newSessionBtn) {
      newSessionBtn.addEventListener('click', async function() {
        if (isChatBusy) return;
        
        const confirmMsg = languageManager.currentLang === 'en' 
          ? 'Start a new chat session? Current conversation will be saved.'
          : 'Mulai sesi chat baru? Percakapan saat ini akan disimpan.';
        
        if (confirm(confirmMsg)) {
          try {
            const response = await fetch('/api/chat/new-session', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
              }
            });
            
            if (response.ok) {
              // Reload greeting with current language
              languageManager.reloadGreeting();
              messageCount = 0;
              
              const successMsg = languageManager.currentLang === 'en'
                ? 'New session started!'
                : 'Sesi baru dimulai!';
              
              Swal.fire({
                icon: 'success',
                title: successMsg,
                timer: 1500,
                showConfirmButton: false,
                customClass: {
                  popup: 'rounded-3xl border-none shadow-2xl',
                  title: 'font-black'
                }
              });
            }
          } catch (error) {
            console.error('Failed to start new session:', error);
          }
        }
      });
    }

    // Copy message function (global scope)
    window.copyMessage = function(msgId) {
      const msgElement = document.getElementById(msgId);
      if (!msgElement) return;
      
      const textContent = msgElement.querySelector('.prose')?.innerText || msgElement.querySelector('.bg-white')?.innerText;
      if (!textContent) return;
      
      navigator.clipboard.writeText(textContent).then(() => {
        // Show temporary success message
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        btn.classList.add('text-green-600');
        setTimeout(() => {
          btn.innerHTML = originalHTML;
          btn.classList.remove('text-green-600');
        }, 1500);
      }).catch(() => {
        alert('Gagal menyalin teks');
      });
    };

    // Feedback function (global scope)
    window.feedbackMessage = function(msgId, type) {
      const btn = event.target.closest('button');
      const wasActive = btn.classList.contains('feedback-active');
      
      // Remove active state from all feedback buttons in this message
      const msgElement = document.getElementById(msgId);
      msgElement.querySelectorAll('.feedback-active').forEach(b => {
        b.classList.remove('feedback-active', 'text-green-600', 'text-red-600');
      });
      
      if (!wasActive) {
        btn.classList.add('feedback-active');
        btn.classList.add(type === 'like' ? 'text-green-600' : 'text-red-600');
        
        // Send feedback to server (optional analytics)
        fetch('/api/chat/feedback', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
          },
          body: JSON.stringify({ message_id: msgId, type: type })
        }).catch(() => {
          // Silent fail
        });
      }
    };
    
    // ===== VOICE INPUT HANDLER =====
    const voiceInputBtn = document.getElementById('voice-input-btn');
    
    if (voiceInputBtn) {
      // Check if voice input is supported
      if (!voiceInput.isSupported) {
        voiceInputBtn.style.display = 'none';
      } else {
        voiceInputBtn.addEventListener('click', async function() {
          if (isChatBusy || isRecording) return;
          
          isRecording = true;
          voiceInputBtn.classList.add('voice-recording', 'bg-red-500', 'text-white');
          voiceInputBtn.title = 'Merekam... (klik untuk berhenti)';
          
          try {
            const transcript = await voiceInput.start();
            chatInput.value = transcript;
            chatInput.focus();
          } catch (error) {
            console.error('Voice input error:', error);
            if (error !== 'no-speech' && error !== 'aborted') {
              Swal.fire({
                icon: 'error',
                title: languageManager.translate('voiceInputError'),
                text: languageManager.translate('voiceInputErrorText'),
                confirmButtonColor: '#2563eb',
                customClass: {
                  popup: 'rounded-3xl border-none shadow-2xl',
                  title: 'font-black'
                }
              });
            }
          } finally {
            isRecording = false;
            voiceInputBtn.classList.remove('voice-recording', 'bg-red-500', 'text-white');
            voiceInputBtn.title = 'Input Suara';
          }
        });
      }
    }
    
    // ===== VOICE OUTPUT HANDLER (Global function) =====
    window.speakMessage = function(msgId) {
      const msgElement = document.getElementById(msgId);
      if (!msgElement) return;
      
      const btn = event.target.closest('button');
      const textContent = msgElement.querySelector('.prose')?.innerText || msgElement.querySelector('.bg-white')?.innerText;
      if (!textContent) return;
      
      // If already playing this message, stop it
      if (btn.classList.contains('voice-playing')) {
        voiceOutput.stop();
        btn.classList.remove('voice-playing', 'text-blue-600');
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>';
        return;
      }
      
      // Stop any other playing voice
      document.querySelectorAll('.voice-playing').forEach(b => {
        b.classList.remove('voice-playing', 'text-blue-600');
        b.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>';
      });
      
      // Start playing
      btn.classList.add('voice-playing', 'text-blue-600');
      btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>';
      
      voiceOutput.speak(textContent)
        .then(() => {
          btn.classList.remove('voice-playing', 'text-blue-600');
          btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>';
        })
        .catch((error) => {
          console.error('Voice output error:', error);
          btn.classList.remove('voice-playing', 'text-blue-600');
          btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>';
        });
    };
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
