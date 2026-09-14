  <aside id="sidebarMenu" class="print:hidden fixed top-0 left-0 z-40 w-64 h-screen transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 bg-white text-slate-800 border-r border-slate-200/80 shadow-sm flex flex-col font-sans">
    <!-- Branding Logo -->
    <div class="h-16 flex items-center justify-start border-b border-slate-100 px-6 shrink-0 bg-white">
      <div class="flex items-center gap-3">
        <img src="{{ asset('logo.png') }}" class="w-8 h-8 object-contain" alt="Logo RPJMD Pasuruan" />
        <div>
          <div class="font-extrabold text-base tracking-tight text-slate-900 leading-none">RPJMD <span class="text-blue-600 font-normal">Admin</span></div>
          <div class="text-[10px] uppercase font-semibold text-slate-400 tracking-wider mt-0.5">Kab. Pasuruan</div>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1.5" id="nav-container">
      <div class="mb-4">
        <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Main Menu</p>
        
        <x-admin.nav-link route="admin.dashboard" module="dashboard" :active="$adminModule === 'dashboard'">
          <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
          <span class="font-medium text-sm">Dashboard</span>
        </x-admin.nav-link>
      </div>

      <div class="mb-4">
        <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Dokumen & Basis Data AI</p>

        <x-admin.nav-link route="admin.dokumen.index" module="dokumen" :active="$adminModule === 'dokumen'">
          <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          <span class="font-medium text-sm">Bank Dokumen</span>
        </x-admin.nav-link>

        <x-admin.nav-link route="admin.ingest.index" module="ingest" :active="$adminModule === 'ingest'" class="mt-1">
          <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
          <span class="font-medium text-sm">Ingest AI Chatbot</span>
        </x-admin.nav-link>
      </div>

      @if(Auth::user()->role === 'Super Admin')
      <div class="mt-6 mb-4">
        <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pengaturan System</p>
        
        <x-admin.nav-link route="admin.pengguna.index" module="pengguna" :active="$adminModule === 'pengguna'">
          <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
          <span class="font-medium text-sm">Kelola Pengguna</span>
        </x-admin.nav-link>

        <x-admin.nav-link route="admin.setelan.index" module="setelan" :active="$adminModule === 'setelan'" class="mt-1">
          <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
          <span class="font-medium text-sm">Setelan Sistem</span>
        </x-admin.nav-link>
      </div>
      @endif
    </div>

    <!-- Footer Actions -->
    <div class="p-4 border-t border-slate-100 space-y-2 bg-slate-50/50">
      <a href="{{ route('home') }}" class="flex justify-center items-center py-2 px-4 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors shadow-2xs">
        Kembali ke Portal Publik
      </a>
      <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="w-full flex justify-center items-center py-2 px-4 rounded-xl bg-rose-50 text-rose-600 font-semibold hover:bg-rose-600 hover:text-white transition-all text-xs border border-rose-100 hover:border-transparent">
            Logout
          </button>
      </form>
    </div>
  </aside>

  <div id="overlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-30 hidden lg:hidden transition-opacity print:hidden"></div>
