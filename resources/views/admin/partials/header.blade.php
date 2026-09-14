    <!-- Topbar -->
    <header class="sticky top-0 z-20 w-full h-20 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-8 shrink-0 print:hidden">
      <div class="flex items-center flex-1">
        <button id="hamburgerBtn" type="button" aria-label="Buka menu navigasi" aria-controls="sidebarMenu" aria-expanded="false" class="lg:hidden p-2 text-gray-500 hover:text-blue-600 hover:bg-gray-100 rounded-lg focus:outline-none transition-colors mr-4">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>

        <!-- Functional Searchbar -->
        <form method="GET" action="{{ url()->current() }}" class="w-full max-w-lg hidden sm:block">
          @foreach(request()->except(['q', 'page']) as $key => $value)
            @if(is_scalar($value))<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif
          @endforeach
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
            </span>
            <input id="admin-search" type="search" name="q" aria-label="Cari pada halaman ini" value="{{ request('q') }}" data-admin-filter="q" placeholder="Cari berita, layanan, pengguna..." autocomplete="off"
              class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:bg-white focus:border-blue-500 block pl-10 pr-4 p-2.5 transition-colors">
            <!-- Search result count hint -->
            <span id="search-hint" class="hidden absolute right-3 top-2.5 text-xs text-gray-400 font-medium"></span>
          </div>
        </form>
      </div>

      <div class="flex items-center gap-3 md:gap-5">

        <!-- Bell Notification Dropdown -->
        <div class="relative" id="notif-wrapper">
          <button id="notif-btn" type="button" aria-label="Notifikasi aspirasi" aria-controls="notif-dropdown" aria-expanded="false" class="relative p-2 text-gray-400 hover:text-blue-600 bg-gray-50 border border-gray-200 rounded-full transition-colors focus:outline-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            
            @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white text-[9px] font-black rounded-full border-2 border-white flex items-center justify-center px-0.5">
              {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
            @endif
          </button>

          <!-- Dropdown panel -->
          <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
              <h3 class="font-black text-gray-900 text-sm">Pesan Aspirasi Baru</h3>
              @if($unreadCount > 0)
              <span class="px-2 py-0.5 bg-red-100 text-red-600 text-xs font-bold rounded-full">{{ $unreadCount }} belum dibaca</span>
              @else
              <span class="px-2 py-0.5 bg-green-100 text-green-600 text-xs font-bold rounded-full">Semua selesai</span>
              @endif
            </div>
            <div class="max-h-72 overflow-y-auto divide-y divide-gray-50">
              
              @forelse($latestContacts as $msg)
              <a href="{{ route('admin.aspirasi.index') }}" class="block px-5 py-3.5 hover:bg-blue-50 transition-colors cursor-pointer group">
                <div class="flex justify-between items-start mb-1">
                  <p class="font-bold text-gray-900 text-sm truncate max-w-[160px] group-hover:text-blue-700 transition-colors">{{ $msg->name }}</p>
                  <span class="text-[10px] text-gray-400 flex-shrink-0 ml-2">{{ $msg->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-xs text-gray-600 font-semibold truncate">{{ $msg->subject }}</p>
                <p class="text-[11px] text-gray-400 truncate mt-0.5">{{ $msg->message }}</p>
                <p class="text-[10px] text-blue-500 font-bold mt-1 opacity-0 group-hover:opacity-100 transition-opacity">Klik untuk lihat semua →</p>
              </a>
              @empty
              <div class="py-8 text-center text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4"></path></svg>
                <p class="text-xs font-bold">Tidak ada pesan baru</p>
              </div>
              @endforelse
            </div>
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50">
              <a href="{{ route('admin.aspirasi.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline w-full text-center block transition-colors">
                Lihat Semua Pesan Aspirasi →
              </a>
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
