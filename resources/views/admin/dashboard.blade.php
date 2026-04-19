@extends('layouts.admin')

@section('title', 'RPJMD Admin - Control Panel')

@section('content')
      <!-- ============================== -->
      <!-- SECTION: DASHBOARD (DEFAULT)   -->
      <!-- ============================== -->
      <section id="section-dashboard" class="content-section block">
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-600 text-green-700 font-bold rounded-r-lg shadow-sm">
            {{ session('success') }}
        </div>
        @endif

        {{-- Chart Panel — di atas halaman dashboard --}}
        <div id="chart-panel" class="mb-6 bg-white rounded-2xl border border-gray-200 shadow-sm p-8" style="display:none;">
          <!-- Hidden for Print Header -->
          <div class="hidden print:block mb-8 pb-6 border-b border-gray-200">
             <div class="text-center mb-6">
               <h1 class="text-2xl font-black text-gray-900 uppercase tracking-widest">Laporan Progres RPJMD Kota Pasuruan</h1>
               <p class="text-gray-500 text-sm mt-1">Dicetak pada {{ now()->format('d M Y, H:i') }}</p>
             </div>
             
             <!-- Print Only Stats: Hero Stats -->
             <div class="mb-8 break-inside-avoid text-left">
               <h3 class="text-[11px] font-black uppercase text-gray-500 tracking-widest mb-3 border-b border-gray-300 pb-2">I. Data Referensi Strategis</h3>
               <div class="grid grid-cols-4 border-l border-t border-gray-300">
                 @foreach($heroStats as $st)
                 <div class="p-3 border-r border-b border-gray-300 text-center">
                   <p class="text-[9px] font-bold text-gray-500 uppercase">{{ $st->label }}</p>
                   <p class="text-base font-black text-gray-900 mt-1">{{ $st->value }}</p>
                 </div>
                 @endforeach
               </div>
             </div>

             <!-- Print Only Stats: Capaian Stats -->
             <div class="mb-4 break-inside-avoid text-left">
               <h3 class="text-[11px] font-black uppercase text-gray-800 tracking-widest mb-3 border-b border-gray-300 pb-2">II. Metrik Capaian Kinerja (RPJMD)</h3>
               <div class="grid grid-cols-4 border-l border-t border-gray-300">
                 @foreach($capaianStats as $st)
                 <div class="p-3 border-r border-b border-gray-300 text-center bg-gray-50 print:bg-transparent">
                   <p class="text-[9px] font-bold text-gray-600 uppercase">{{ $st->label }}</p>
                   <p class="text-base font-black text-gray-900 mt-1">{{ $st->value }}{{ $st->key == 'total_progress' ? '%' : '' }}</p>
                 </div>
                 @endforeach
               </div>
             </div>
          </div>
          <div class="flex items-center justify-between mb-6 print:hidden">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
              </div>
              <div>
                <h2 class="text-lg font-black text-gray-900">Grafik Progres Capaian Indikator</h2>
                <p class="text-xs text-gray-400">Visualisasi rata-rata capaian per sektor RPJMD.</p>
              </div>
            </div>
            <button id="toggle-chart-btn" onclick="toggleChartPanel()" class="text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-xl transition-colors">
              Sembunyikan Grafik
            </button>
          </div>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="flex flex-col items-center">
              <p class="text-sm font-bold text-gray-500 mb-4 uppercase tracking-widest text-center">Rata-rata Progress per Sektor</p>
              <div style="max-width: 280px; width: 100%;">
                <canvas id="chart-doughnut"></canvas>
              </div>
            </div>
            <div>
              <p class="text-sm font-bold text-gray-500 mb-4 uppercase tracking-widest">Detail Indikator Semua Sektor</p>
              <canvas id="chart-bar" style="max-height: 300px;"></canvas>
            </div>
          </div>
        </div>

        {{-- Summary Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8 print:hidden">
          {{-- Total Berita --}}
          <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-.586-1.414l-4.5-4.5A2 2 0 0012.586 3H12"></path></svg>
              </div>
              <div>
                <p class="text-2xl font-black text-gray-900 leading-tight">{{ $counts['news'] }}</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Total Berita</p>
              </div>
            </div>
          </div>

          {{-- Layanan Aktif --}}
          <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
              </div>
              <div>
                <p class="text-2xl font-black text-gray-900 leading-tight">{{ $counts['services'] }}</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Layanan Aktif</p>
              </div>
            </div>
          </div>

          {{-- Pesan Baru --}}
          <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
              </div>
              <div>
                <p class="text-2xl font-black text-gray-900 leading-tight">{{ $counts['unread_contacts'] }}</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Pesan Baru</p>
              </div>
            </div>
          </div>

          {{-- Total Admin --}}
          <div class="bg-gradient-to-br from-blue-600 to-blue-900 rounded-2xl p-6 text-white relative overflow-hidden group shadow-xl">
            <div class="absolute top-[-20%] right-[-10%] w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all"></div>
            <div class="relative z-10">
              <h3 class="text-[10px] font-bold text-blue-200 uppercase tracking-widest mb-1">Total Admin</h3>
              <p class="text-3xl font-black">{{ $counts['users'] }}</p>
              <p class="text-[10px] text-blue-300 font-medium">Staf Pengelola Sistem</p>
            </div>
          </div>
        </div>

        {{-- Stats Editors (Separated) + Activity Log --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

          {{-- LEFT COLUMN: Two separate stat editors stacked --}}
          <div class="lg:col-span-2 space-y-6">

            {{-- CARD 1: Statistik Utama Beranda --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm print:hidden">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-yellow-50 text-yellow-500 flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                </div>
                <div>
                  <h2 class="text-lg font-black text-gray-900">Statistik Utama Halaman Beranda</h2>
                  <p class="text-xs text-gray-400">Angka yang tampil di panel bawah hero section. Anda dapat menambah atau mengurangi datanya.</p>
                </div>
              </div>
              
              {{-- Form Edit Data Saat Ini --}}
              <form action="{{ route('admin.update_hero_stats') }}" method="POST" class="mb-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-4">
                  @forelse($heroStats as $st)
                  <div class="relative bg-gray-50 p-4 border border-gray-100 rounded-xl">
                    <div class="space-y-1.5 mb-2">
                       <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest block truncate" title="{{ $st->label }}">{{ $st->label }}</label>
                       <input type="text" name="hero_stats[{{ $st->key }}]" value="{{ $st->value }}"
                         class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-400/20 focus:border-yellow-400 font-bold text-gray-900 text-sm">
                    </div>
                    <div class="absolute top-3 right-3">
                      <button type="button" onclick="event.preventDefault(); if(confirm('Hapus statistik ini?')) document.getElementById('delete-hero-{{ $st->id }}').submit();" class="text-red-400 hover:text-red-600 bg-white shadow-sm border border-red-100 rounded-full w-6 h-6 flex items-center justify-center transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                      </button>
                    </div>
                  </div>
                  @empty
                  <div class="col-span-full py-4 text-center text-sm text-gray-400">Belum ada data statistik beranda. Silakan tambah baru.</div>
                  @endforelse
                </div>
                <div class="flex justify-end pt-2">
                  <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-yellow-900 font-bold py-2.5 px-6 rounded-xl shadow-sm transition-all active:scale-95 text-sm">
                    Simpan Nilai Beranda
                  </button>
                </div>
              </form>

              {{-- Hidden Delete Forms --}}
              @foreach($heroStats as $st)
              <form id="delete-hero-{{ $st->id }}" action="{{ route('admin.delete_hero_stat', $st->id) }}" method="POST" class="hidden">
                  @csrf @method('DELETE')
              </form>
              @endforeach

              {{-- Form Tambah Data Baru --}}
              <div class="pt-6 border-t border-gray-100">
                <h3 class="text-xs font-bold text-gray-700 uppercase mb-3">Tambah Statistik Baru</h3>
                <form action="{{ route('admin.store_hero_stat') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                  @csrf
                  <input type="text" name="label" required placeholder="Label (mis. Jumlah Desa)" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white outline-none focus:border-yellow-400">
                  <input type="text" name="value" required placeholder="Nilai (mis. 34)" class="w-full sm:w-32 px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white outline-none focus:border-yellow-400">
                  <button type="submit" class="bg-gray-900 hover:bg-black text-white font-bold py-2 px-4 rounded-lg text-sm whitespace-nowrap">
                    + Tambah
                  </button>
                </form>
              </div>
            </div>

            {{-- CARD 2: Statistik Capaian RPJMD --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm">
              <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                  </div>
                  <div>
                    <h2 class="text-lg font-black text-gray-900">Statistik Capaian RPJMD</h2>
                    <p class="text-xs text-gray-400">Angka yang tampil di halaman Capaian & grafik progres.</p>
                  </div>
                </div>
                <button type="button" onclick="window.print()" class="print:hidden bg-green-50 hover:bg-green-100 text-green-700 font-bold py-2 px-4 flex items-center gap-2 rounded-xl border border-green-200 transition-colors shadow-sm text-sm">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2-2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                  Cetak PDF Laporan
                </button>
              </div>
              <form action="{{ route('admin.update_stats') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-6 print:hidden">
                @csrf
                @foreach($capaianStats as $st)
                <div class="space-y-2">
                  <label class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ $st->label }}</label>
                  <div class="relative">
                    <input type="text" name="stats[{{ $st->key }}]" value="{{ $st->value }}"
                      class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all font-bold text-gray-900">
                    @if($st->key == 'total_progress') <span class="absolute right-4 top-3.5 font-bold text-gray-400">%</span> @endif
                  </div>
                </div>
                @endforeach
                <div class="sm:col-span-3 pt-4 border-t border-gray-100 flex justify-end">
                  <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-blue-500/20 transition-all active:scale-95">
                    Simpan Capaian RPJMD &rarr;
                  </button>
                </div>
              </form>
            </div>

          </div>{{-- end left column --}}

          {{-- Activity Log --}}
          <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm">
            <h2 class="text-xl font-black text-gray-900 mb-5">Log Aktivitas</h2>
            <div class="space-y-3 overflow-y-auto" style="max-height: 480px;">
              @forelse($activities as $act)
              <div class="flex gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors border border-gray-100">
                <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center font-black text-[10px]
                  {{ $act->action == 'Hapus' ? 'bg-red-50 text-red-600' : ($act->action == 'Buat' ? 'bg-green-50 text-green-600' : 'bg-blue-50 text-blue-600') }}">
                  {{ strtoupper(substr($act->type, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex justify-between items-start gap-1">
                    <span class="text-[10px] font-black uppercase tracking-wide {{ $act->action == 'Hapus' ? 'text-red-500' : ($act->action == 'Buat' ? 'text-green-500' : 'text-blue-500') }}">
                      {{ $act->action }} {{ $act->type }}
                    </span>
                    <span class="text-[9px] text-gray-400 font-semibold flex-shrink-0 whitespace-nowrap">{{ $act->created_at->diffForHumans() }}</span>
                  </div>
                  <p class="text-xs text-gray-600 truncate mt-0.5">{{ $act->description }}</p>
                  <p class="text-[9px] text-gray-400 italic mt-0.5">— {{ $act->user->name ?? 'Sistem' }}</p>
                </div>
              </div>
              @empty
              <div class="py-10 flex flex-col items-center justify-center text-gray-400">
                <svg class="w-10 h-10 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="font-bold text-sm">Belum ada aktivitas</p>
              </div>
              @endforelse
            </div>
          </div>
        </div>
      </section>


      <!-- ============================== -->
      <!-- SECTION: MANAJEMEN BERITA      -->
      <!-- ============================== -->
      <section id="section-berita" class="content-section hidden">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
          <div>
            <h1 class="text-3xl font-black text-gray-900 mb-1">Manajemen Berita</h1>
            <p class="text-gray-500 text-sm">Organisir manuskrip berita harian dan pembaruan sistem Anda.</p>
          </div>
          <button onclick="showSection('section-berita-form')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-lg shadow cursor-pointer transition-colors w-max text-sm">
            Tulis Artikel Baru
          </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="p-6 border-b border-gray-200 flex flex-wrap gap-4 items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Daftar Publikasi Harian</h2>
          </div>
          
          <div class="w-full overflow-x-auto">
            <table class="w-full min-w-[700px] text-left border-collapse">
              <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                  <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-16">Gambar</th>
                  <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Judul Artikel</th>
                  <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                  <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 whitespace-nowrap">
                @foreach($news as $post)
                <tr class="hover:bg-blue-50/50 transition-colors">
                  {{-- Thumbnail --}}
                  <td class="px-4 py-3">
                    @if($post->image_url)
                      <img src="{{ asset($post->image_url) }}" alt="{{ $post->title }}"
                        class="w-12 h-12 object-cover rounded-lg border border-gray-200 shadow-sm">
                    @else
                      <div class="w-12 h-12 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                      </div>
                    @endif
                  </td>
                  <td class="px-6 py-4">
                    <div class="font-bold text-gray-900 text-sm max-w-xs truncate">{{ $post->title }}</div>
                    <div class="text-xs text-gray-500">{{ $post->created_at->format('d M Y') }}</div>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600 font-medium">{{ $post->category }}</td>
                  <td class="px-6 py-4">
                    @if($post->is_published)
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold bg-green-100 text-green-700 rounded-md">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Publik
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold bg-yellow-100 text-yellow-700 rounded-md">
                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Draft
                      </span>
                    @endif
                  </td>
                  <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                    {{-- Toggle Publik/Draft --}}
                    <form action="{{ route('admin.toggle_publish', $post->id) }}" method="POST" class="inline toggle-form">
                      @csrf
                      <button type="submit"
                        data-title="{{ $post->is_published ? 'Jadikan Draft?' : 'Publikasikan?' }}"
                        data-text="{{ $post->is_published ? 'Berita tidak akan tampil di halaman publik.' : 'Berita akan tampil di halaman publik.' }}"
                        data-confirm="{{ $post->is_published ? 'Ya, Jadikan Draft' : 'Ya, Publikasikan' }}"
                        data-color="{{ $post->is_published ? '#d97706' : '#16a34a' }}"
                        class="toggle-btn text-xs px-3 py-1 rounded transition-colors
                          {{ $post->is_published ? 'bg-yellow-50 text-yellow-700 hover:bg-yellow-500 hover:text-white' : 'bg-green-50 text-green-700 hover:bg-green-500 hover:text-white' }}">
                        {{ $post->is_published ? 'Jadikan Draft' : 'Publikasikan' }}
                      </button>
                    </form>
                    @if($post->is_published)
                      <a href="{{ route('berita.detail', $post->slug) }}" target="_blank"
                         class="text-xs px-3 py-1 bg-indigo-50 text-indigo-600 rounded hover:bg-indigo-600 hover:text-white transition-colors inline-block">
                        Lihat
                      </a>
                    @endif
                    <button onclick="editBerita({{ json_encode($post) }})" class="text-xs px-3 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-600 hover:text-white transition-colors">Edit</button>
                    <form action="{{ route('admin.delete_news', $post->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Hapus berita ini?')" class="text-xs px-3 py-1 bg-gray-50 text-gray-600 rounded hover:text-red-600 transition-colors">Hapus</button>
                    </form>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- SECTION: BERITA FORM (EDIT) -->
      <section id="section-berita-edit" class="content-section hidden">
        <div class="flex items-center gap-4 mb-8">
          <button onclick="showSection('section-berita')" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </button>
          <h1 class="text-3xl font-black text-gray-900">Edit Berita</h1>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 max-w-4xl">
          <form id="form-edit-berita" action="" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Judul Berita</label>
                <input type="text" name="title" id="edit-title" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
              </div>
              <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Kategori</label>
                <select name="category" id="edit-category" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
                  <option value="Program Strategis">Program Strategis</option>
                  <option value="Infrastruktur">Infrastruktur</option>
                  <option value="Musrenbang">Musrenbang</option>
                  <option value="Ekonomi">Ekonomi</option>
                </select>
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Ganti Gambar (Opsional)</label>
              <!-- Current image preview -->
              <div id="edit-current-image-wrapper" class="hidden mb-3">
                <p class="text-xs text-gray-400 mb-1.5 font-medium">Gambar saat ini:</p>
                <div class="relative w-fit">
                  <img id="edit-current-image" src="" alt="Gambar saat ini"
                    class="w-48 h-32 object-cover rounded-xl border border-gray-200 shadow-sm">
                  <span class="absolute top-1 right-1 bg-black/50 text-white text-[9px] px-1.5 py-0.5 rounded font-bold uppercase">Aktif</span>
                </div>
              </div>
              <input type="file" name="image" accept="image/*" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
              <p class="text-xs text-gray-400">Kosongkan jika tidak ingin mengganti gambar.</p>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Konten Berita</label>
              <textarea name="content" id="edit-content" required rows="10" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all"></textarea>
            </div>
            <div class="space-y-3 pt-2 border-t border-gray-100">
              <label class="text-sm font-bold text-gray-700">Status Publikasi</label>
              <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="is_published" id="edit-pub-yes" value="1" class="hidden">
                  <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-gray-200 cursor-pointer transition-all hover:border-green-200
                            has-[:checked]:bg-green-50 has-[:checked]:border-green-300" >
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span>
                    <p class="text-sm font-bold text-gray-500">Publik</p>
                  </div>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="is_published" id="edit-pub-no" value="0" class="hidden">
                  <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-gray-200 cursor-pointer transition-all hover:border-yellow-200
                            has-[:checked]:bg-yellow-50 has-[:checked]:border-yellow-300">
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span>
                    <p class="text-sm font-bold text-gray-500">Draft</p>
                  </div>
                </label>
                <p class="text-xs text-gray-400 italic">Publik = tampil di situs. Draft = disembunyikan.</p>
              </div>
            </div>
            <button type="submit" class="px-8 py-3 rounded-xl font-bold bg-blue-600 text-white hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Berita</button>
          </form>
        </div>
      </section>

      <!-- ============================== -->
      <!-- SECTION: FORM BERITA (SUB-SPA) -->
      <!-- ============================== -->
      <section id="section-berita-form" class="content-section hidden">
        <div class="flex items-center gap-4 mb-8">
          <button onclick="showSection('section-berita')" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </button>
          <div>
            <h1 class="text-3xl font-black text-gray-900">Tulis/Edit Berita</h1>
            <p class="text-gray-500 text-sm">Kembali ke daftar berita dengan menekan tombol panah.</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 max-w-4xl">
          <form action="{{ route('admin.store_news') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Judul Artikel</label>
                <input type="text" name="title" required placeholder="Masukkan judul menarik..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
              </div>
              <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Kategori</label>
                <select name="category" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                  <option value="Musrenbang">Musrenbang</option>
                  <option value="Infrastruktur">Infrastruktur</option>
                  <option value="Kesehatan">Kesehatan</option>
                  <option value="Lingkungan">Lingkungan</option>
                  <option value="Dokumen Resmi">Dokumen Resmi</option>
                </select>
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Gambar Artikel</label>
              <input type="file" name="image" accept="image/*" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
              <p class="text-xs text-gray-400 font-medium italic">Format: JPG, PNG, GIF. Ukuran maks: 2MB.</p>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Konten Berita</label>
              <textarea name="content" required rows="8" placeholder="Tulis rincian berita di sini..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all"></textarea>
            </div>
            <div class="space-y-3 pt-2 border-t border-gray-100">
              <label class="text-sm font-bold text-gray-700">Status Publikasi</label>
              <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="is_published" value="1" checked class="hidden peer/pub">
                  <div class="peer-checked/pub:[&_span]:bg-green-500 peer-checked/pub:[&_div]:bg-green-50 peer-checked/pub:[&_div]:border-green-300 peer-checked/pub:[&_p]:text-green-700
                            flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-gray-200 cursor-pointer transition-all hover:border-green-200
                            has-[:checked]:bg-green-50 has-[:checked]:border-green-300" id="label-pub-create">
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-300 transition-colors" id="dot-pub-create"></span>
                    <p class="text-sm font-bold text-gray-500 transition-colors" id="text-pub-create">Publik</p>
                  </div>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="is_published" value="0" class="hidden peer/draft">
                  <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-gray-200 cursor-pointer transition-all hover:border-yellow-200
                            has-[:checked]:bg-yellow-50 has-[:checked]:border-yellow-300" id="label-draft-create">
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-300 transition-colors" id="dot-draft-create"></span>
                    <p class="text-sm font-bold text-gray-500 transition-colors" id="text-draft-create">Draft</p>
                  </div>
                </label>
                <p class="text-xs text-gray-400 italic">Publik = tampil di situs. Draft = disembunyikan.</p>
              </div>
            </div>
            <div class="flex justify-end gap-3 pt-4">
              <button type="button" onclick="showSection('section-berita')" class="px-6 py-3 rounded-xl font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">Batalkan</button>
              <button type="submit" class="px-8 py-3 rounded-xl font-bold bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition-all">Simpan Berita</button>
            </div>
          </form>
        </div>
      </section>

      <!-- ============================== -->
      <!-- SECTION: LAYANAN PUBLIK        -->
      <!-- ============================== -->
      <section id="section-layanan" class="content-section hidden">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
          <div>
            <h1 class="text-3xl font-black text-gray-900 mb-1">Manajemen Layanan</h1>
            <p class="text-gray-500 text-sm">Kelola tombol dan link di halaman portal layanan Anda.</p>
          </div>
          <button onclick="showSection('section-layanan-form')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-lg shadow cursor-pointer transition-colors w-max text-sm">
            Tambah Layanan Baru +
          </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="w-full overflow-x-auto">
            <table class="w-full min-w-[700px] text-left border-collapse">
              <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Layanan</th>
                  <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">URL Link</th>
                  <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 whitespace-nowrap">
                @foreach($services as $sv)
                <tr class="hover:bg-blue-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="font-bold text-gray-900 text-sm">{{ $sv->name }}</div>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">{{ $sv->url }}</td>
                  <td class="px-6 py-4 text-right space-x-2">
                    <button onclick="editLayanan({{ json_encode($sv) }})" class="text-xs px-3 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-600 hover:text-white transition-colors">Edit</button>
                    <form action="{{ route('admin.delete_service', $sv->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Hapus layanan ini?')" class="text-xs px-3 py-1 bg-gray-50 text-gray-600 rounded hover:text-red-600 transition-colors">Hapus</button>
                    </form>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        <!-- Section: Pesan Masuk (Separated for clarity) -->
        <h1 class="text-2xl font-black text-gray-900 mb-1 mt-12">Tiket Aspirasi & Pesan</h1>
        <p class="text-gray-500 text-sm mb-8">Daftar pesan masuk dari halaman Hubungi Kami.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          @forelse($contacts as $msg)
          <div class="bg-white border-l-4 {{ $msg->status == 'resolved' ? 'border-l-green-500 opacity-75' : 'border-l-red-500' }} rounded-xl p-6 shadow-sm border border-gray-200 transition-all">
            <div class="flex justify-between items-start mb-4">
              <span class="px-2 py-1 {{ $msg->status == 'resolved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} text-[10px] font-black uppercase tracking-widest rounded-md">
                {{ $msg->status == 'resolved' ? 'Selesai' : 'Belum Dibaca' }}
              </span>
              <span class="text-xs text-gray-400">{{ $msg->created_at->diffForHumans() }}</span>
            </div>
            <h3 class="font-bold text-gray-900 mb-1">{{ $msg->subject }}</h3>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Dari: {{ $msg->name }} ({{ $msg->email }})</p>
            <p class="text-gray-600 text-sm mb-4 leading-relaxed">{{ $msg->message }}</p>
            
            @if($msg->status == 'unread')
            <div class="pt-4 border-t border-gray-100">
               <form action="{{ route('admin.resolve_contact', $msg->id) }}" method="POST" class="resolve-form">
                 @csrf
                 <button type="button"
                   data-name="{{ $msg->name }}"
                   class="resolve-btn text-[11px] font-black text-blue-600 hover:text-green-600 uppercase tracking-tighter flex items-center gap-1 transition-colors">
                   Tandai Selesai
                   <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                 </button>
               </form>
            </div>
            @endif
          </div>
          @empty
          <div class="col-span-full py-12 text-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 text-gray-400 font-bold">
            Belum ada pesan aspirasi masuk.
          </div>
          @endforelse
        </div>
      </section>

      <!-- SECTION: FORM LAYANAN -->
      <section id="section-layanan-form" class="content-section hidden">
        <div class="flex items-center gap-4 mb-8">
          <button onclick="showSection('section-layanan')" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </button>
          <h1 class="text-3xl font-black text-gray-900">Tambah Layanan</h1>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 max-w-4xl">
          <form action="{{ route('admin.store_service') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Nama Layanan</label>
                <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
              </div>
              <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Icon (Identifier)</label>
                <select name="icon" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
                  <option value="document-report">Document Report</option>
                  <option value="chart-bar">Chart Bar</option>
                  <option value="office-building">Office Building</option>
                </select>
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">URL / Link</label>
              <input type="text" name="url" required placeholder="https://..." class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Deskripsi Singkat</label>
              <textarea name="description" required rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none"></textarea>
            </div>
            <button type="submit" class="px-8 py-3 rounded-xl font-bold bg-blue-600 text-white hover:bg-blue-700 transition-all">Simpan Layanan</button>
          </form>
        </div>
      </section>

      <!-- SECTION: LAYANAN FORM (EDIT) -->
      <section id="section-layanan-edit" class="content-section hidden">
        <div class="flex items-center gap-4 mb-8">
          <button onclick="showSection('section-layanan')" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </button>
          <h1 class="text-3xl font-black text-gray-900">Edit Layanan</h1>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 max-w-4xl">
          <form id="form-edit-layanan" action="" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Nama Layanan</label>
                <input type="text" name="name" id="edit-sv-name" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
              </div>
              <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Icon (Identifier)</label>
                <select name="icon" id="edit-sv-icon" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
                  <option value="document-report">Document Report</option>
                  <option value="chart-bar">Chart Bar</option>
                  <option value="office-building">Office Building</option>
                </select>
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">URL / Link</label>
              <input type="text" name="url" id="edit-sv-url" required placeholder="https://..." class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Deskripsi Singkat</label>
              <textarea name="description" id="edit-sv-description" required rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all"></textarea>
            </div>
            <button type="submit" class="px-8 py-3 rounded-xl font-bold bg-blue-600 text-white hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Layanan</button>
          </form>
        </div>
      </section>

      <!-- ============================== -->
      <!-- SECTION: MANAJEMEN CAPAIAN     -->
      <!-- ============================== -->
      <section id="section-capaian" class="content-section hidden">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
          <div>
            <h1 class="text-3xl font-black text-gray-900 mb-1">Manajemen Capaian & Indikator</h1>
            <p class="text-gray-500 text-sm">Kelola metrik capaian RPJMD yang akan ditampilkan secara otomatis di portal publik.</p>
          </div>
          <div class="flex gap-2">
            <button onclick="showSection('section-sector-form')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors text-sm">
              + Sektor Baru
            </button>
            <button onclick="showSection('section-indicator-form')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors text-sm">
              + Indikator Baru
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-8">
          @foreach($sectors as $sector)
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Sector Header -->
            <div class="p-6 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-{{ $sector->theme_color }}-100 text-{{ $sector->theme_color }}-600 flex flex-col justify-center items-center">
                  {!! $sector->icon !!}
                </div>
                <div>
                  <h2 class="text-lg font-black text-gray-900">{{ $sector->name }}</h2>
                  <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Tema: {{ $sector->theme_color }}</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button type="button" onclick="editSector({{ $sector->id }}, '{{ addslashes($sector->name) }}', '{{ $sector->theme_color }}', '{{ base64_encode($sector->icon) }}')" class="cursor-pointer text-xs px-3 py-1 bg-indigo-50 text-indigo-600 font-bold rounded hover:bg-indigo-100 transition-colors">Edit Sektor</button>
                <form action="{{ route('admin.delete_sector', $sector->id) }}" method="POST">
                  @csrf @method('DELETE')
                  <button type="submit" onclick="return confirm('AMARAN: Menghapus Sektor ini AKAN menghapus seluruh indikator di dalamnya. Pastikan?')" class="cursor-pointer text-xs px-3 py-1 bg-red-100 text-red-600 rounded hover:bg-red-600 hover:text-white transition-colors">
                    Hapus Sektor
                  </button>
                </form>
              </div>
            </div>

            <!-- Indicators Table -->
            <div class="w-full overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead class="bg-white border-b border-gray-100">
                  <tr>
                    <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest w-2/3">Nama Indikator</th>
                    <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Progress</th>
                    <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                  @forelse($sector->indicators as $ind)
                  <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3 font-bold text-sm text-gray-800">{{ $ind->name }}</td>
                    <td class="px-6 py-3">
                      <div class="flex items-center gap-2">
                        <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                          <div class="h-full bg-{{ $sector->theme_color }}-500" style="width: {{ $ind->progress }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-600">{{ $ind->progress }}%</span>
                      </div>
                    </td>
                    <td class="px-6 py-3 text-right">
                      <div class="flex items-center justify-end gap-2">
                        <button type="button" onclick="editIndicator({{ $ind->id }}, {{ $ind->sector_id }}, '{{ addslashes($ind->name) }}', {{ $ind->progress }})" class="cursor-pointer text-xs px-2 py-1 text-blue-600 hover:bg-blue-50 rounded transition-colors font-bold">Edit</button>
                        <form action="{{ route('admin.delete_indicator', $ind->id) }}" method="POST" class="inline">
                          @csrf @method('DELETE')
                          <button type="submit" onclick="return confirm('Hapus indikator ini?')" class="cursor-pointer text-xs px-2 py-1 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded transition-colors">Hapus</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-xs font-bold text-gray-400">Belum ada indikator.</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
          @endforeach
        </div>

        {{-- Chart Ringkas — hanya di dalam section-capaian --}}
        @if($sectors->count() > 0)
        <div class="mt-8 bg-white rounded-2xl border border-gray-200 shadow-sm p-8">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <div>
              <h2 class="text-lg font-black text-gray-900">Ringkasan Grafik Capaian</h2>
              <p class="text-xs text-gray-400">Progres rata-rata seluruh sektor RPJMD.</p>
            </div>
          </div>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div class="flex flex-col items-center">
              <p class="text-sm font-bold text-gray-500 mb-4 uppercase tracking-widest text-center">Rata-rata per Sektor</p>
              <div style="max-width: 260px; width: 100%;">
                <canvas id="chart-capaian-doughnut"></canvas>
              </div>
            </div>
            <div>
              <p class="text-sm font-bold text-gray-500 mb-4 uppercase tracking-widest">Semua Indikator</p>
              <canvas id="chart-capaian-bar" style="max-height: 280px;"></canvas>
            </div>
          </div>
        </div>
        @endif
      </section>

      <!-- SECTION: SEKTOR FORM (ADD) -->
      <section id="section-sector-form" class="content-section hidden">
        <div class="flex items-center gap-4 mb-8">
          <button onclick="showSection('section-capaian')" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </button>
          <h1 class="text-3xl font-black text-gray-900">Tambah Sektor Capaian</h1>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 max-w-2xl">
          <form action="{{ route('admin.store_sector') }}" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Nama Sektor Prioritas</label>
              <input type="text" name="name" required placeholder="Contoh: Infrastruktur" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Tema Warna Tailwind</label>
              <select name="theme_color" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
                <option value="blue">Biru (Blue)</option>
                <option value="yellow">Kuning (Yellow)</option>
                <option value="green">Hijau (Green)</option>
                <option value="red">Merah (Red)</option>
                <option value="purple">Ungu (Purple)</option>
                <option value="indigo">Indigo</option>
              </select>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">SVG Icon (Copy dari Heroicons)</label>
              <textarea name="icon" required rows="3" placeholder='<svg ...></svg>' class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none font-mono text-xs"></textarea>
            </div>
            <button type="submit" class="px-8 py-3 rounded-xl font-bold bg-indigo-600 text-white hover:bg-indigo-700 transition-all">Simpan Sektor</button>
          </form>
        </div>
      </section>

      <!-- SECTION: INDIKATOR FORM (ADD) -->
      <section id="section-indicator-form" class="content-section hidden">
        <div class="flex items-center gap-4 mb-8">
          <button onclick="showSection('section-capaian')" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </button>
          <h1 class="text-3xl font-black text-gray-900">Tambah Indikator Capaian</h1>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 max-w-2xl">
          <form action="{{ route('admin.store_indicator') }}" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Pilih Sektor Induk</label>
              <select name="sector_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
                @foreach($sectors as $sec)
                  <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Nama Indikator / Program</label>
              <input type="text" name="name" required placeholder="Contoh: Akses Air Minum Layak" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Progress Capaian (%)</label>
              <input type="number" name="progress" min="0" max="100" required placeholder="0-100" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
            </div>
            <button type="submit" class="px-8 py-3 rounded-xl font-bold bg-blue-600 text-white hover:bg-blue-700 transition-all">Simpan Indikator</button>
          </form>
        </div>
      </section>

      <!-- SECTION: SEKTOR FORM (EDIT) -->
      <section id="section-sector-edit" class="content-section hidden">
        <div class="flex items-center gap-4 mb-8">
          <button onclick="showSection('section-capaian')" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </button>
          <h1 class="text-3xl font-black text-gray-900">Edit Sektor Capaian</h1>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 max-w-2xl">
          <form id="form-edit-sector" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Nama Sektor Prioritas</label>
              <input type="text" id="edit-sector-name" name="name" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Tema Warna Tailwind</label>
              <select id="edit-sector-theme" name="theme_color" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
                <option value="blue">Biru (Blue)</option>
                <option value="yellow">Kuning (Yellow)</option>
                <option value="green">Hijau (Green)</option>
                <option value="red">Merah (Red)</option>
                <option value="purple">Ungu (Purple)</option>
                <option value="indigo">Indigo</option>
              </select>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">SVG Icon (Copy dari Heroicons)</label>
              <textarea id="edit-sector-icon" name="icon" required rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none font-mono text-xs"></textarea>
            </div>
            <button type="submit" class="px-8 py-3 rounded-xl font-bold bg-indigo-600 text-white hover:bg-indigo-700 transition-all">Update Sektor</button>
          </form>
        </div>
      </section>

      <!-- SECTION: INDIKATOR FORM (EDIT) -->
      <section id="section-indicator-edit" class="content-section hidden">
        <div class="flex items-center gap-4 mb-8">
          <button onclick="showSection('section-capaian')" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </button>
          <h1 class="text-3xl font-black text-gray-900">Edit Indikator Capaian</h1>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 max-w-2xl">
          <form id="form-edit-indicator" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Pilih Sektor Induk</label>
              <select id="edit-indicator-sector" name="sector_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
                @foreach($sectors as $sec)
                  <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Nama Indikator / Program</label>
              <input type="text" id="edit-indicator-name" name="name" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Progress Capaian (%)</label>
              <input type="number" id="edit-indicator-progress" name="progress" min="0" max="100" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
            </div>
            <button type="submit" class="px-8 py-3 rounded-xl font-bold bg-blue-600 text-white hover:bg-blue-700 transition-all">Update Indikator</button>
          </form>
        </div>
      </section>

      <!-- ============================== -->
      <!-- SECTION: PENGGUNA SISTEM       -->
      <!-- ============================== -->
      @if(Auth::user()->role === 'Super Admin')
      <section id="section-pengguna" class="content-section hidden">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
          <div>
            <h1 class="text-3xl font-black text-gray-900 mb-1">Kelola Pengguna</h1>
            <p class="text-gray-500 text-sm">Administrasi kepegawaian dan tingkat otorisasi login.</p>
          </div>
          <button onclick="showSection('section-pengguna-form')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-lg shadow cursor-pointer transition-colors w-max text-sm">
            + Tambah Pegawai Baru
          </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama & Email</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 italic font-medium">
              @foreach($users as $u)
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                  <div class="font-bold text-gray-900 not-italic">{{ $u->name }}</div>
                  <div class="text-xs text-gray-500">{{ $u->email }}</div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 text-[10px] font-black uppercase rounded {{ $u->role == 'Super Admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ $u->role }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                  <button onclick="editUser({{ json_encode($u) }})" class="text-xs px-3 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-600 hover:text-white transition-colors">Edit</button>
                  @if($u->id != auth()->id())
                  <form action="{{ route('admin.delete_user', $u->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Hapus pengguna ini?')" class="text-xs px-3 py-1 bg-red-50 text-red-600 rounded hover:bg-red-600 hover:text-white transition-colors">Hapus</button>
                  </form>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </section>

      <!-- SECTION: PENGGUNA FORM (ADD) -->
      <section id="section-pengguna-form" class="content-section hidden">
        <div class="flex items-center gap-4 mb-8">
          <button onclick="showSection('section-pengguna')" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </button>
          <h1 class="text-3xl font-black text-gray-900">Tambah Pegawai</h1>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 max-w-2xl">
          <form action="{{ route('admin.store_user') }}" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Nama Lengkap</label>
              <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
              </div>
              <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Role</label>
                <select name="role" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
                  <option value="Admin">Admin</option>
                  <option value="Super Admin">Super Admin</option>
                </select>
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Password Sementara</label>
              <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
            </div>
            <button type="submit" class="w-full py-4 rounded-xl font-bold bg-blue-600 text-white hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Daftarkan Pegawai</button>
          </form>
        </div>
      </section>

      <!-- SECTION: PENGGUNA FORM (EDIT) -->
      <section id="section-pengguna-edit" class="content-section hidden">
        <div class="flex items-center gap-4 mb-8">
          <button onclick="showSection('section-pengguna')" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </button>
          <h1 class="text-3xl font-black text-gray-900">Edit Pegawai</h1>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 max-w-2xl">
          <form id="form-edit-user" action="" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Nama Lengkap</label>
              <input type="text" name="name" id="edit-user-name" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Email</label>
                <input type="email" name="email" id="edit-user-email" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
              </div>
              <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Role</label>
                <select name="role" id="edit-user-role" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none">
                  <option value="Admin">Admin</option>
                  <option value="Super Admin">Super Admin</option>
                </select>
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Password Baru (Opsional)</label>
              <input type="password" name="password" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none" placeholder="Kosongkan jika tidak ingin ganti">
            </div>
            <button type="submit" class="w-full py-4 rounded-xl font-bold bg-blue-600 text-white hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Data Pegawai</button>
          </form>
        </div>
      </section>

      <!-- ============================== -->
      <!-- SECTION: SETELAN SISTEM        -->
      <!-- ============================== -->
      <section id="section-setelan" class="content-section hidden">
        <h1 class="text-3xl font-black text-gray-900 mb-1">Setelan Konfigurasi</h1>
        <p class="text-gray-500 text-sm mb-8">Pengaturan umum server RPJMD dan visibilitas portal.</p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
          <!-- General Settings -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <h2 class="text-xl font-black text-gray-900 mb-6 border-b border-gray-100 pb-4">Setelan Umum</h2>
            <form class="space-y-6">
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Instansi (Header)</label>
                <input type="text" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm" value="RPJMD Kota Pasuruan">
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Email Pemulihan Notifikasi</label>
                <input type="email" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm" value="admin@rpjmd.pasuruankota.go.id">
              </div>
              <div class="flex items-center gap-3">
                <input type="checkbox" id="maintenance" class="w-5 h-5 text-blue-600 rounded">
                <label for="maintenance" class="font-bold text-gray-700 text-sm">Mode Pemeliharaan</label>
              </div>
              <button type="button" class="px-6 py-2.5 bg-gray-100 text-gray-400 font-bold rounded-lg cursor-not-allowed">Simpan Setelan</button>
            </form>
          </div>

          <!-- Profile Content Editor -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <h2 class="text-xl font-black text-gray-900 mb-6 border-b border-gray-100 pb-4">Editor Konten Profil</h2>
            <form action="{{ route('admin.update_profile') }}" method="POST" class="space-y-6">
              @csrf
              @foreach($profiles as $prof)
              <div class="space-y-2">
                <label class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ $prof->title }}</label>
                @if($prof->key == 'misi')
                  <textarea name="profiles[{{ $prof->key }}]" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all font-medium text-gray-700 text-sm" placeholder="Gunakan pemisah | untuk butir misi baru">{{ $prof->content }}</textarea>
                  <p class="text-[10px] text-blue-500 italic mt-1 font-bold">* Gunakan simbol garis tegak (|) untuk membedakan butir-butir misi.</p>
                @else
                  <textarea name="profiles[{{ $prof->key }}]" rows="4" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all font-medium text-gray-700 text-sm">{{ $prof->content }}</textarea>
                @endif
              </div>
              @endforeach
              <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-blue-500/20 transition-all">
                  Update Konten Profil &rarr;
                </button>
              </div>
            </form>
          </div>
        </div>
      </section>
      @endif
@endsection

@push('scripts')
<script>
  function editBerita(news) {
    const form = document.getElementById('form-edit-berita');
    form.action = `/admin/berita/${news.id}`;

    document.getElementById('edit-title').value    = news.title;
    document.getElementById('edit-category').value = news.category;
    document.getElementById('edit-content').value  = news.content;

    // Set radio Publik/Draft sesuai status artikel
    document.getElementById(news.is_published ? 'edit-pub-yes' : 'edit-pub-no').checked = true;

    // Tampilkan preview gambar saat ini jika ada
    const imgWrapper = document.getElementById('edit-current-image-wrapper');
    const imgEl      = document.getElementById('edit-current-image');
    if (news.image_url) {
      imgEl.src = '/' + news.image_url;
      imgWrapper.classList.remove('hidden');
    } else {
      imgWrapper.classList.add('hidden');
      imgEl.src = '';
    }

    showSection('section-berita-edit');
  }

  function editLayanan(sv) {
    const form = document.getElementById('form-edit-layanan');
    form.action = `/admin/layanan/${sv.id}`;

    document.getElementById('edit-sv-name').value        = sv.name;
    document.getElementById('edit-sv-url').value         = sv.url;
    document.getElementById('edit-sv-icon').value        = sv.icon;
    document.getElementById('edit-sv-description').value = sv.description;

    showSection('section-layanan-edit');
  }

  function editUser(user) {
    const form = document.getElementById('form-edit-user');
    form.action = `/admin/users/${user.id}`;

    document.getElementById('edit-user-name').value  = user.name;
    document.getElementById('edit-user-email').value = user.email;
    document.getElementById('edit-user-role').value  = user.role;

    showSection('section-pengguna-edit');
  }

  function editSector(id, name, themeColor, base64Icon) {
    const form = document.getElementById('form-edit-sector');
    form.action = `/admin/sector/${id}`;
    
    document.getElementById('edit-sector-name').value = name;
    document.getElementById('edit-sector-theme').value = themeColor;
    document.getElementById('edit-sector-icon').value = atob(base64Icon);
    
    showSection('section-sector-edit');
  }

  function editIndicator(id, sectorId, name, progress) {
    const form = document.getElementById('form-edit-indicator');
    form.action = `/admin/indicator/${id}`;
    
    document.getElementById('edit-indicator-sector').value = sectorId;
    document.getElementById('edit-indicator-name').value = name;
    document.getElementById('edit-indicator-progress').value = progress;
    
    showSection('section-indicator-edit');
  }

  // Toggle Publik/Draft dengan SweetAlert konfirmasi
  document.querySelectorAll('.toggle-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const form = this.closest('form');
      Swal.fire({
        title: this.dataset.title,
        text: this.dataset.text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: this.dataset.color,
        cancelButtonColor: '#6b7280',
        confirmButtonText: this.dataset.confirm,
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-3xl shadow-2xl', title: 'font-black' }
      }).then(result => {
        if (result.isConfirmed) form.submit();
      });
    });
  });

  // ===== CHART.JS INIT =====
  (function initCharts() {
    const sectors = @json($sectors);
    if (!sectors || sectors.length === 0) return;

    // Show panel
    document.getElementById('chart-panel').style.display = 'block';

    // Build chart data
    const sectorLabels = sectors.map(s => s.name);
    const sectorAvgProgress = sectors.map(s => {
      const inds = s.indicators;
      if (!inds || inds.length === 0) return 0;
      const sum = inds.reduce((a, b) => a + b.progress, 0);
      return Math.round(sum / inds.length);
    });

    const palette = [
      '#3b82f6','#facc15','#10b981','#f97316','#8b5cf6','#ec4899','#14b8a6','#f43f5e'
    ];
    const sectorColors = sectors.map((_, i) => palette[i % palette.length]);

    // Doughnut
    const dCtx = document.getElementById('chart-doughnut');
    if (dCtx) {
      new Chart(dCtx, {
        type: 'doughnut',
        data: {
          labels: sectorLabels,
          datasets: [{
            data: sectorAvgProgress,
            backgroundColor: sectorColors,
            borderWidth: 3,
            borderColor: '#fff',
            hoverBorderWidth: 4,
          }]
        },
        options: {
          cutout: '72%',
          plugins: {
            legend: { position: 'bottom', labels: { font: { weight: 'bold', size: 11 }, padding: 16, usePointStyle: true } },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw}%` } }
          }
        }
      });
    }

    // Bar — all indicators
    const barLabels = [];
    const barData = [];
    const barColors = [];
    sectors.forEach((sector, si) => {
      (sector.indicators || []).forEach(ind => {
        barLabels.push(ind.name.length > 20 ? ind.name.substring(0, 20) + '…' : ind.name);
        barData.push(ind.progress);
        barColors.push(sectorColors[si % sectorColors.length]);
      });
    });

    const bCtx = document.getElementById('chart-bar');
    if (bCtx && barLabels.length > 0) {
      new Chart(bCtx, {
        type: 'bar',
        data: {
          labels: barLabels,
          datasets: [{
            label: 'Progress (%)',
            data: barData,
            backgroundColor: barColors.map(c => c + 'cc'),
            borderColor: barColors,
            borderWidth: 2,
            borderRadius: 8,
          }]
        },
        options: {
          indexAxis: barLabels.length > 6 ? 'y' : 'x',
          scales: {
            x: { beginAtZero: true, max: 100, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
            y: { ticks: { font: { size: 10, weight: 'bold' } }, grid: { display: false } }
          },
          plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` ${ctx.raw}%` } }
          }
        }
      });
    }

    // ===== CHART CAPAIAN SECTION =====
    const cdCtx = document.getElementById('chart-capaian-doughnut');
    if (cdCtx) {
      new Chart(cdCtx, {
        type: 'doughnut',
        data: {
          labels: sectorLabels,
          datasets: [{ data: sectorAvgProgress, backgroundColor: sectorColors, borderWidth: 3, borderColor: '#fff', hoverBorderWidth: 4 }]
        },
        options: {
          cutout: '70%',
          plugins: {
            legend: { position: 'bottom', labels: { font: { weight: 'bold', size: 11 }, padding: 14, usePointStyle: true } },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw}%` } }
          }
        }
      });
    }

    const cbCtx = document.getElementById('chart-capaian-bar');
    if (cbCtx && barLabels.length > 0) {
      new Chart(cbCtx, {
        type: 'bar',
        data: {
          labels: barLabels,
          datasets: [{ label: 'Progress (%)', data: barData, backgroundColor: barColors.map(c => c + 'cc'), borderColor: barColors, borderWidth: 2, borderRadius: 8 }]
        },
        options: {
          indexAxis: barLabels.length > 6 ? 'y' : 'x',
          scales: {
            x: { beginAtZero: true, max: 100, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
            y: { ticks: { font: { size: 10, weight: 'bold' } }, grid: { display: false } }
          },
          plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.raw}%` } } }
        }
      });
    }
  })();

  function toggleChartPanel() {
    const panel = document.getElementById('chart-panel');
    const btn = document.getElementById('toggle-chart-btn');
    const isVisible = panel.style.display !== 'none';
    panel.style.display = isVisible ? 'none' : 'block';
    btn.textContent = isVisible ? 'Tampilkan Grafik' : 'Sembunyikan Grafik';
  }

  // SweetAlert konfirmasi "Tandai Selesai" untuk aspirasi
  document.querySelectorAll('.resolve-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const name = this.dataset.name;
      const form = this.closest('.resolve-form');
      Swal.fire({
        title: 'Tandai Selesai?',
        text: `Pesan dari "${name}" akan ditandai sebagai selesai.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Tandai Selesai',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-3xl shadow-2xl', title: 'font-black' }
      }).then(result => {
        if (result.isConfirmed) form.submit();
      });
    });
  });
</script>
@endpush
