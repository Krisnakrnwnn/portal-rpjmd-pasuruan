<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'RPJMD Admin - Control Panel')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  
  <style>
    .active-nav {
      @apply bg-blue-600 text-white shadow-lg;
    }
  </style>
  @stack('styles')
</head>
<body class="font-sans text-gray-800 bg-gray-50 min-h-screen">

  <aside id="sidebarMenu" class="print:hidden fixed top-0 left-0 z-40 w-64 h-screen transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 bg-[#03112E] text-white shadow-2xl flex flex-col">
    <!-- Branding Logo -->
    <div class="h-20 flex items-center justify-center border-b border-indigo-900/30 px-6 shrink-0 bg-[#020a1c]">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center shadow-lg">
          <span class="text-white font-black text-xl">R</span>
        </div>
        <div>
          <div class="font-black text-lg tracking-wide text-white leading-tight">ADMIN PANEL</div>
          <div class="text-[10px] uppercase font-bold text-blue-400 tracking-wider">Halaman Pusat</div>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto py-6 px-4 space-y-2" id="nav-container">
      <div class="mb-4">
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Umum</p>
        
        <button data-target="section-dashboard" class="nav-btn flex items-center px-4 py-3 bg-blue-600 text-white rounded-lg shadow-lg transition-all w-full text-left active">
          <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
          <span class="font-semibold text-sm">Dashboard Utama</span>
        </button>
      </div>

      <button data-target="section-berita" class="nav-btn flex items-center px-4 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-all w-full text-left mt-2">
        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-.586-1.414l-4.5-4.5A2 2 0 0012.586 3H12"></path></svg>
        <span class="font-medium text-sm">Manajemen Berita</span>
      </button>

      <button data-target="section-layanan" class="nav-btn flex items-center px-4 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-all w-full text-left mt-2">
        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
        <span class="font-medium text-sm">Katalog Layanan</span>
      </button>

      <button data-target="section-capaian" class="nav-btn flex items-center px-4 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-all w-full text-left mt-2">
        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
        <span class="font-medium text-sm">Data Capaian</span>
      </button>

      @if(Auth::user()->role === 'Super Admin')
      <div class="mt-8 mb-4">
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Konfigurasi</p>
        
        <button data-target="section-pengguna" class="nav-btn flex items-center px-4 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-all w-full text-left">
          <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
          <span class="font-medium text-sm">Kelola Pengguna</span>
        </button>

        <button data-target="section-setelan" class="nav-btn flex items-center px-4 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-all w-full text-left mt-2">
          <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
          <span class="font-medium text-sm">Setelan Sistem</span>
        </button>
      </div>
      @endif
    </div>

    <!-- Pintu Keluar -->
    <div class="px-6 py-4 border-t border-indigo-900/50 space-y-3">
      <a href="{{ route('home') }}" class="flex justify-center items-center py-2 px-4 rounded-lg bg-indigo-900/40 text-blue-200 text-xs font-bold hover:bg-indigo-900 transition-colors">
        Kembali ke Portal
      </a>
      <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="w-full flex justify-center items-center py-2.5 px-4 rounded-lg bg-red-600/20 text-red-400 font-bold hover:bg-red-600 hover:text-white transition-all text-sm">
            Logout Sekarang
          </button>
      </form>
    </div>
  </aside>

  <div id="overlay" class="fixed inset-0 bg-gray-900/60 z-30 hidden lg:hidden transition-opacity print:hidden"></div>

  <div class="lg:ml-64 flex flex-col min-h-screen">
    
    <!-- Topbar -->
    <header class="sticky top-0 z-20 w-full h-20 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-8 shrink-0 print:hidden">
      <div class="flex items-center flex-1">
        <button id="hamburgerBtn" type="button" class="lg:hidden p-2 text-gray-500 hover:text-blue-600 hover:bg-gray-100 rounded-lg focus:outline-none transition-colors mr-4">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>

        <!-- Functional Searchbar -->
        <div class="w-full max-w-lg hidden sm:block">
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
            </span>
            <input id="admin-search" type="text" placeholder="Cari berita, layanan, pengguna..." autocomplete="off"
              class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:bg-white focus:border-blue-500 block pl-10 pr-4 p-2.5 transition-colors">
            <!-- Search result count hint -->
            <span id="search-hint" class="hidden absolute right-3 top-2.5 text-xs text-gray-400 font-medium"></span>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-3 md:gap-5">

        <!-- Bell Notification Dropdown -->
        <div class="relative" id="notif-wrapper">
          <button id="notif-btn" class="relative p-2 text-gray-400 hover:text-blue-600 bg-gray-50 border border-gray-200 rounded-full transition-colors focus:outline-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            @php $unreadCount = \App\Models\Contact::where('status','unread')->count(); @endphp
            @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white text-[9px] font-black rounded-full border-2 border-white flex items-center justify-center px-0.5">
              {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
            @endif
          </button>

          <!-- Dropdown panel -->
          <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
              <h3 class="font-black text-gray-900 text-sm">Pesan Aspirasi Baru</h3>
              @if($unreadCount > 0)
              <span class="px-2 py-0.5 bg-red-100 text-red-600 text-xs font-bold rounded-full">{{ $unreadCount }} belum dibaca</span>
              @else
              <span class="px-2 py-0.5 bg-green-100 text-green-600 text-xs font-bold rounded-full">Semua selesai</span>
              @endif
            </div>
            <div class="max-h-72 overflow-y-auto divide-y divide-gray-50">
              @php $latestContacts = \App\Models\Contact::where('status','unread')->latest()->take(5)->get(); @endphp
              @forelse($latestContacts as $msg)
              <div onclick="goToAspirasi()" class="px-5 py-3.5 hover:bg-blue-50 transition-colors cursor-pointer group">
                <div class="flex justify-between items-start mb-1">
                  <p class="font-bold text-gray-900 text-sm truncate max-w-[160px] group-hover:text-blue-700 transition-colors">{{ $msg->name }}</p>
                  <span class="text-[10px] text-gray-400 flex-shrink-0 ml-2">{{ $msg->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-xs text-gray-600 font-semibold truncate">{{ $msg->subject }}</p>
                <p class="text-[11px] text-gray-400 truncate mt-0.5">{{ $msg->message }}</p>
                <p class="text-[10px] text-blue-500 font-bold mt-1 opacity-0 group-hover:opacity-100 transition-opacity">Klik untuk lihat semua →</p>
              </div>
              @empty
              <div class="py-8 text-center text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4"></path></svg>
                <p class="text-xs font-bold">Tidak ada pesan baru</p>
              </div>
              @endforelse
            </div>
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50">
              <button onclick="goToAspirasi()" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline w-full text-center block transition-colors">
                Lihat Semua Pesan Aspirasi →
              </button>
            </div>
          </div>
        </div>

        <div class="h-6 w-px bg-gray-200 hidden md:block"></div>
        <div class="flex items-center gap-3">
          <div class="hidden md:block text-right">
            <div class="text-sm font-bold text-gray-900 leading-none">{{ Auth::user()->name }}</div>
            <div class="text-[11px] font-semibold text-gray-500 mt-1 uppercase tracking-tighter">{{ Auth::user()->role }}</div>
          </div>
          <img class="w-10 h-10 rounded-full object-cover border border-gray-300 shadow-sm" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563eb&color=fff" alt="User Profile">
        </div>
      </div>
    </header>

    <main class="flex-1 p-4 lg:p-8 relative overflow-x-hidden print:p-0">
      @yield('content')
    </main>
  </div>

  <script>
    const navButtons = document.querySelectorAll('.nav-btn');
    const sections = document.querySelectorAll('.content-section');
    const sidebarMenu = document.getElementById("sidebarMenu");
    const overlay = document.getElementById("overlay");

    function showSection(targetId) {
      sections.forEach(sec => {
        sec.classList.remove('block');
        sec.classList.add('hidden');
      });

      const targetSec = document.getElementById(targetId);
      if(targetSec) {
        targetSec.classList.remove('hidden');
        targetSec.classList.add('block');
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }

      navButtons.forEach(btn => {
        const btnTarget = btn.getAttribute('data-target');
        if(targetId.includes(btnTarget)) {
           btn.classList.add('bg-blue-600', 'text-white', 'shadow-lg');
           btn.classList.remove('text-gray-300', 'hover:bg-white/10');
        } else {
           btn.classList.remove('bg-blue-600', 'text-white', 'shadow-lg');
           btn.classList.add('text-gray-300', 'hover:bg-white/10');
        }
      });
    }

    function toggleSidebar() {
      sidebarMenu.classList.toggle("-translate-x-full");
      overlay.classList.toggle("hidden");
    }

    document.addEventListener("DOMContentLoaded", () => {
      document.getElementById("hamburgerBtn")?.addEventListener("click", toggleSidebar);
      overlay?.addEventListener("click", toggleSidebar);

      navButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          const targetId = btn.getAttribute('data-target');
          showSection(targetId);
          if(window.innerWidth < 1024) toggleSidebar();
          // Clear search when switching sections
          const searchInput = document.getElementById('admin-search');
          if (searchInput) { searchInput.value = ''; filterRows(''); }
        });
      });

      // Handle hash based section on load
      const hash = window.location.hash;
      if (hash && hash.startsWith('#section-')) {
          showSection(hash.substring(1));
      }

      // ─── Searchbar: real-time filter ────────────────────────────────────────
      const adminSearch = document.getElementById('admin-search');
      const searchHint  = document.getElementById('search-hint');

      function filterRows(query) {
        const q = query.trim().toLowerCase();

        // Find the currently visible section
        const activeSection = document.querySelector('.content-section.block');
        if (!activeSection) return;

        // Target: <tr> rows OR card-like divs with a data-search-text attribute
        const rows = activeSection.querySelectorAll('tbody tr');
        let visible = 0;

        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          const match = q === '' || text.includes(q);
          row.style.display = match ? '' : 'none';
          if (match) visible++;
        });

        // Update hint
        if (q && searchHint) {
          searchHint.textContent = `${visible} hasil`;
          searchHint.classList.remove('hidden');
        } else if (searchHint) {
          searchHint.classList.add('hidden');
        }
      }

      if (adminSearch) {
        adminSearch.addEventListener('input', () => filterRows(adminSearch.value));
      }

      // ─── Bell Notification Dropdown ─────────────────────────────────────────
      const notifBtn      = document.getElementById('notif-btn');
      const notifDropdown = document.getElementById('notif-dropdown');
      const notifWrapper  = document.getElementById('notif-wrapper');

      if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          notifDropdown.classList.toggle('hidden');
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
          if (!notifWrapper.contains(e.target)) {
            notifDropdown.classList.add('hidden');
          }
        });
      }

      // ─── goToAspirasi: navigasi ke section layanan lalu scroll ke tiket ────
      window.goToAspirasi = function() {
        // Tutup dropdown
        if (notifDropdown) notifDropdown.classList.add('hidden');
        // Navigasi ke section-layanan
        showSection('section-layanan');
        // Scroll ke heading Tiket Aspirasi setelah section muncul
        setTimeout(() => {
          const target = document.querySelector('#section-layanan h1.text-2xl');
          if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }, 150);
      };
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
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
            title: 'Ups!',
            text: "{{ session('error') }}",
            confirmButtonColor: '#2563eb',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black'
            }
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'warning',
            title: 'Validasi Gagal',
            html: '<ul>' +
                @foreach($errors->all() as $error)
                    '<li class="text-sm text-left text-gray-600">{{ $error }}</li>' +
                @endforeach
                '</ul>',
            confirmButtonColor: '#d97706',
            confirmButtonText: 'Perbaiki',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black text-yellow-600'
            }
        });
    @endif
  </script>
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
  @stack('scripts')
</body>
</html>
