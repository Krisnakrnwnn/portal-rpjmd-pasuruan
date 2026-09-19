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
            <input id="admin-search" type="search" name="q" aria-label="Cari pada halaman ini" value="{{ request('q') }}" data-admin-filter="q" placeholder="Cari pada halaman ini..." autocomplete="off"
              class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:bg-white focus:border-blue-500 block pl-10 pr-4 p-2.5 transition-colors">
            <!-- Search result count hint -->
            <span id="search-hint" class="hidden absolute right-3 top-2.5 text-xs text-gray-400 font-medium"></span>
          </div>
        </form>
      </div>

      <div class="flex items-center gap-3 md:gap-5">

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
