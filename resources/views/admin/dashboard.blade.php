@extends('layouts.admin')

@section('title', 'Bapperida Admin - Control Panel')

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
               <h1 class="text-2xl font-black text-gray-900 uppercase tracking-widest">Laporan Progres Bapperida Kabupaten Pasuruan</h1>
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
               <h3 class="text-[11px] font-black uppercase text-gray-800 tracking-widest mb-3 border-b border-gray-300 pb-2">II. Metrik Capaian Kinerja (Bapperida)</h3>
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
            <!-- <button id="toggle-chart-btn" onclick="toggleChartPanel()" class="text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-xl transition-colors">
              Sembunyikan Grafik
            </button> -->
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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 print:hidden">
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

        {{-- Activity Log --}}
          <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm">
            <div class="flex flex-wrap gap-2 items-center justify-between mb-5">
              <h2 class="text-xl font-black text-gray-900">Log Aktivitas</h2>
              {{-- Date Filter --}}
              <input type="date" id="activity-date-filter" oninput="filterActivityByDate(this.value)" onchange="filterActivityByDate(this.value)" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-50 text-gray-600 transition-all cursor-pointer">
            </div>
            
            <div id="activity-list" class="space-y-3 overflow-y-auto pr-2" style="max-height: 480px;">
              @forelse($activities as $act)
              <div class="activity-item flex gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors border border-gray-100" data-date="{{ $act->created_at->setTimezone('Asia/Jakarta')->format('Y-m-d') }}">
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
              {{-- Elemen empty state yang akan muncul kalau data kosong sejak awal atau setelah difilter --}}
              <div id="activity-empty-state" class="text-center py-8 text-gray-400 text-xs">
                  Tidak ada aktivitas pada tanggal ini.
              </div>
              @endforelse
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
            <p class="text-gray-500 text-sm">Mengelola berita harian serta melakukan pembaruan sistem secara berkala.</p>
          </div>
          <button onclick="showSection('section-berita-form')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-lg shadow cursor-pointer transition-colors w-max text-sm">
            Tambah Berita
          </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="p-4 border-b border-gray-200 flex flex-wrap gap-3 items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Daftar Publikasi Harian</h2>
            <div class="flex flex-wrap gap-2 items-center">
              {{-- Search --}}
             
              {{-- Filter Status --}}
              <select id="berita-filter-status" class="text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-50 text-gray-600 transition-all">
                <option value="">Semua Status</option>
                <option value="publik">Publik</option>
                <option value="draft">Draft</option>
              </select>
              {{-- Filter Kategori --}}
              <select id="berita-filter-kategori" class="text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-50 text-gray-600 transition-all">
                <option value="">Semua Kategori</option>
                @foreach($news->pluck('category')->unique()->filter()->values() as $cat)
                  <option value="{{ strtolower($cat) }}">{{ $cat }}</option>
                @endforeach
              </select>
            </div>
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
              <tbody id="berita-tbody" class="divide-y divide-gray-100 whitespace-nowrap">
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
          {{-- Pagination Berita --}}
          <div id="berita-pagination" class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50/50"></div>
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
              <button type="submit" class="px-8 py-3 rounded-xl font-bold bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition-all">Simpan</button>
            </div>
          </form>
        </div>
      </section>

      <!-- SECTION: ASPIRASI & PESAN       -->
      <!-- ============================== -->
      <section id="section-aspirasi" class="content-section hidden">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 gap-4">
          <div>
            <h1 class="text-3xl font-black text-gray-900 mb-1">Aspirasi &amp; Pesan</h1>
            <p class="text-gray-500 text-sm">Daftar pesan masuk dari halaman Hubungi Kami.</p>
          </div>
          {{-- Search & Filter Aspirasi --}}
          <div class="flex flex-wrap gap-2 items-center">
            <div class="flex gap-1 bg-gray-100 rounded-lg p-1">
              <button id="aspirasi-filter-all" onclick="filterAspirasi('all')" class="aspirasi-filter-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all bg-white text-blue-600 shadow-sm">Semua</button>
              <button id="aspirasi-filter-unread" onclick="filterAspirasi('unread')" class="aspirasi-filter-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all text-gray-500 hover:text-gray-700">Belum Selesai</button>
              <button id="aspirasi-filter-resolved" onclick="filterAspirasi('resolved')" class="aspirasi-filter-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all text-gray-500 hover:text-gray-700">Selesai</button>
            </div>
          </div>
        </div>

        <div id="aspirasi-grid" class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
        {{-- Pagination Aspirasi --}}
        <div id="aspirasi-pagination" class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200"></div>
      </section>

      <!-- ============================== -->
      <!-- SECTION: MANAJEMEN CAPAIAN     -->
      <!-- ============================== -->
      <section id="section-capaian" class="content-section hidden">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 gap-4">
          <div>
            <h1 class="text-3xl font-black text-gray-900 mb-1">Manajemen Capaian & Indikator</h1>
            <p class="text-gray-500 text-sm">Kelola metrik capaian RPJMD yang akan ditampilkan secara otomatis di portal publik.</p>
          </div>
          <div class="flex flex-wrap gap-2 items-center">
            {{-- Search Capaian --}}
            <div class="relative">
              <input id="capaian-search" type="text" placeholder="Cari sektor/indikator..." class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-50 w-52 transition-all">
            </div>
            <button onclick="showSection('section-sector-form')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors text-sm">
              + Sektor Baru
            </button>
            <button onclick="showSection('section-indicator-form')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors text-sm">
              + Indikator Baru
            </button>
          </div>
        </div>

        <div id="capaian-grid" class="grid grid-cols-1 gap-8">
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
        {{-- Pagination Capaian --}}
        <div id="capaian-pagination" class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200"></div>

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
              <label class="text-sm font-bold text-gray-700">Pilih Icon Sektor</label>
              <input type="hidden" name="icon" id="sector-icon-add-value" required>
              <button type="button" onclick="toggleIconPicker('add')" id="sector-icon-add-btn"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border-2 border-dashed border-gray-300 hover:border-indigo-400 bg-gray-50 hover:bg-indigo-50 transition-all text-left">
                <div id="sector-icon-add-preview" class="w-9 h-9 rounded-lg bg-gray-200 text-gray-500 flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16"/></svg>
                </div>
                <span id="sector-icon-add-label" class="text-sm font-bold text-gray-400 flex-1">-- Klik untuk memilih icon --</span>
                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
              <div id="sector-icon-add-dropdown" class="hidden z-50 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-200 p-5">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Pilih icon yang sesuai dengan sektor</p>
                <div id="sector-icon-add-grid" class="grid grid-cols-4 sm:grid-cols-5 gap-2"></div>
              </div>
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
              <label class="text-sm font-bold text-gray-700">Pilih Icon Sektor</label>
              <input type="hidden" name="icon" id="sector-icon-edit-value" required>
              <button type="button" onclick="toggleIconPicker('edit')" id="sector-icon-edit-btn"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border-2 border-dashed border-gray-300 hover:border-indigo-400 bg-gray-50 hover:bg-indigo-50 transition-all text-left">
                <div id="sector-icon-edit-preview" class="w-9 h-9 rounded-lg bg-gray-200 text-gray-500 flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16"/></svg>
                </div>
                <span id="sector-icon-edit-label" class="text-sm font-bold text-gray-400 flex-1">-- Klik untuk memilih icon --</span>
                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
              <div id="sector-icon-edit-dropdown" class="hidden z-50 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-200 p-5">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Pilih icon yang sesuai dengan sektor</p>
                <div id="sector-icon-edit-grid" class="grid grid-cols-4 sm:grid-cols-5 gap-2"></div>
              </div>
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
            <tbody id="pengguna-tbody" class="divide-y divide-gray-100 italic font-medium">
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
          {{-- Pagination Pengguna --}}
          <div id="pengguna-pagination" class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50/50"></div>
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
            <h2 class="text-xl font-black text-gray-900 mb-6 border-b border-gray-100 pb-4">Pengaturan Model AI Chatbot</h2>
            <form action="{{ route('admin.update_settings') }}" method="POST" class="space-y-6">
              @csrf
              @php
                $activeModel = \App\Models\Stat::where('key', 'gemini_model')->first()->value ?? 'gemini-2.5-flash';
              @endphp
              <div>
                <label for="gemini_model" class="block text-sm font-bold text-gray-700 mb-2">Model Google Gemini Aktif</label>
                <div class="relative rounded-lg shadow-sm">
                  <input type="text" name="gemini_model" id="gemini_model" 
                    value="{{ $activeModel }}" 
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm font-semibold text-gray-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all"
                    placeholder="Contoh: gemini-2.5-flash" required>
                </div>
                <p class="text-xs text-gray-400 mt-2">Anda dapat mengetik nama model secara manual untuk mendukung model Gemini versi terbaru yang belum terdaftar.</p>
              </div>

              <div>
                <span class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Pilihan Cepat (Klik untuk memilih):</span>
                <div class="flex flex-wrap gap-2">
                  <button type="button" onclick="selectModel('gemini-2.5-flash')" 
                    class="model-badge px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all duration-200 {{ $activeModel === 'gemini-2.5-flash' ? 'bg-blue-600 border-blue-600 text-white shadow-md' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100 hover:border-gray-300' }}">
                    Gemini 2.5 Flash
                  </button>
                  <button type="button" onclick="selectModel('gemini-2.5-pro')" 
                    class="model-badge px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all duration-200 {{ $activeModel === 'gemini-2.5-pro' ? 'bg-blue-600 border-blue-600 text-white shadow-md' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100 hover:border-gray-300' }}">
                    Gemini 2.5 Pro
                  </button>
                  <button type="button" onclick="selectModel('gemini-1.5-flash')" 
                    class="model-badge px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all duration-200 {{ $activeModel === 'gemini-1.5-flash' ? 'bg-blue-600 border-blue-600 text-white shadow-md' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100 hover:border-gray-300' }}">
                    Gemini 1.5 Flash
                  </button>
                  <button type="button" onclick="selectModel('gemini-1.5-pro')" 
                    class="model-badge px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all duration-200 {{ $activeModel === 'gemini-1.5-pro' ? 'bg-blue-600 border-blue-600 text-white shadow-md' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100 hover:border-gray-300' }}">
                    Gemini 1.5 Pro
                  </button>
                </div>
              </div>

              <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs text-gray-500">
                  <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                  Model: <strong class="text-gray-700 font-bold" id="active-model-display">{{ $activeModel }}</strong>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-md active:scale-95 text-sm">
                  Simpan Setelan
                </button>
              </div>
            </form>
          </div>

          <!-- Info Panel -->
          <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-xl p-8 text-white flex flex-col justify-between">
            <div>
              <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              </div>
              <h3 class="text-xl font-black mb-2">Panduan Editor Profil</h3>
              <p class="text-blue-100 text-sm leading-relaxed">Gunakan editor di bawah ini untuk mengatur konten yang ditampilkan di halaman <strong>Profil</strong> pada portal publik.</p>
            </div>
            <ul class="mt-6 space-y-2 text-sm text-blue-100">
              <li class="flex items-start gap-2"><span class="text-yellow-300 font-black mt-0.5">✦</span> <span><strong class="text-white">Sejarah:</strong> Narasi latar belakang Kabupaten Pasuruan</span></li>
              <li class="flex items-start gap-2"><span class="text-yellow-300 font-black mt-0.5">✦</span> <span><strong class="text-white">Visi:</strong> Pernyataan visi singkat dan kuat (satu kalimat)</span></li>
              <li class="flex items-start gap-2"><span class="text-yellow-300 font-black mt-0.5">✦</span> <span><strong class="text-white">Misi:</strong> Gunakan tanda <code class="bg-white/20 px-1 rounded">|</code> sebagai pemisah antar butir misi</span></li>
            </ul>
          </div>
        </div>

        <!-- ===== PROFIL EDITOR SECTION ===== -->
        <div class="mb-4">
          <h2 class="text-xl font-black text-gray-900">Editor Profil Instansi</h2>
          <p class="text-gray-500 text-sm mt-1">Konten ini ditampilkan di halaman Profil portal publik.</p>
        </div>

        <form action="{{ route('admin.update_profile') }}" method="POST" class="space-y-6">
          @csrf

          {{-- Sejarah Card --}}
          @php $sejarah = $profiles->firstWhere('key','sejarah') @endphp
          <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="flex items-center gap-4 px-8 py-5 border-b border-gray-100 bg-amber-50">
              <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
              </div>
              <div>
                <h3 class="font-black text-gray-900">Sejarah Singkat</h3>
                <p class="text-xs text-gray-500 mt-0.5">Narasi latar belakang dan sejarah berdirinya Kabupaten Pasuruan.</p>
              </div>
            </div>
            <div class="p-8">
              <textarea
                name="profiles[sejarah]"
                id="editor-sejarah"
                rows="8"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-300 transition-all font-medium text-gray-700 text-sm leading-relaxed resize-none"
                placeholder="Tuliskan sejarah singkat Kabupaten Pasuruan di sini..."
              >{{ $sejarah?->content }}</textarea>
              <p class="text-xs text-gray-400 mt-2 text-right" id="count-sejarah">0 karakter</p>
            </div>
          </div>

          {{-- Visi Card --}}
          @php $visi = $profiles->firstWhere('key','visi') @endphp
          <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="flex items-center gap-4 px-8 py-5 border-b border-gray-100 bg-blue-50">
              <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
              </div>
              <div>
                <h3 class="font-black text-gray-900">Visi Pembangunan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Pernyataan visi singkat dan kuat (satu kalimat). Akan ditampilkan sebagai kutipan utama.</p>
              </div>
            </div>
            <div class="p-8">
              <textarea
                name="profiles[visi]"
                id="editor-visi"
                rows="3"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-300 transition-all font-bold text-gray-800 text-base leading-relaxed resize-none"
                placeholder="Contoh: Terwujudnya Kabupaten Pasuruan yang Maju, Sejahtera, dan Berkeadilan"
              >{{ $visi?->content }}</textarea>
              <p class="text-xs text-gray-400 mt-2 text-right" id="count-visi">0 karakter</p>
            </div>
          </div>

          {{-- Misi Card --}}
          @php $misi = $profiles->firstWhere('key','misi') @endphp
          <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="flex items-center gap-4 px-8 py-5 border-b border-gray-100 bg-green-50">
              <div class="w-10 h-10 rounded-xl bg-green-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
              </div>
              <div>
                <h3 class="font-black text-gray-900">Misi Pembangunan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Pisahkan tiap butir misi dengan tanda garis tegak <strong class="font-mono bg-gray-100 px-1 rounded text-gray-700">|</strong>. Setiap butir akan otomatis ditampilkan sebagai poin bernomor.</p>
              </div>
            </div>
            <div class="p-8">
              <textarea
                name="profiles[misi]"
                id="editor-misi"
                rows="10"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-green-500/10 focus:border-green-300 transition-all font-medium text-gray-700 text-sm leading-relaxed resize-none"
                placeholder="Misi 1|Misi 2|Misi 3"
              >{{ $misi?->content }}</textarea>
              <div class="flex items-center justify-between mt-2">
                <p class="text-xs text-green-600 font-bold" id="count-misi-butir">0 butir misi terdeteksi</p>
                <p class="text-xs text-gray-400" id="count-misi">0 karakter</p>
              </div>
            </div>
          </div>

          <div class="flex justify-end pt-2">
            <button type="submit" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-10 rounded-2xl shadow-lg shadow-blue-500/25 transition-all hover:scale-105">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              Simpan Semua Perubahan Profil
            </button>
          </div>
        </form>
      </section>
      @endif

      <!-- ============================== -->
      <!-- SECTION: INGEST CHATBOT PDF    -->
      <!-- ============================== -->
      <section id="section-ingest" class="content-section hidden">
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 mb-1">Ingest Data Chatbot</h1>
            <p class="text-gray-500 text-sm">Unggah dokumen PDF RPJMD untuk melatih kecerdasan AI Chatbot Anda.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 sticky top-24">
              <h2 class="text-xl font-black text-gray-900 mb-6">Unggah Dokumen</h2>
              
              <form id="ingest-form" class="space-y-6">
                @csrf
                <div class="space-y-4">
                  <div class="flex items-center justify-center w-full">
                    <label for="pdf_file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all group">
                      <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-10 h-10 mb-3 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p class="mb-2 text-sm text-gray-500 font-bold"><span class="text-blue-600">Klik untuk upload</span> atau drag & drop</p>
                        <p class="text-xs text-gray-400">PDF (Maks. 10MB)</p>
                      </div>
                      <input id="pdf_file" name="pdf_file" type="file" class="hidden" accept=".pdf" required />
                    </label>
                  </div>
                  <div id="file-name-preview" class="hidden text-sm font-bold text-blue-600 bg-blue-50 p-3 rounded-xl flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="truncate"></span>
                  </div>
                </div>

                <button type="submit" id="btn-start-ingest" class="btn-ingest w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-2xl shadow-lg shadow-blue-500/20 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                  <span class="spinner hidden"></span>
                  <span id="btn-text">Mulai Proses Ingest</span>
                </button>
              </form>
            </div>
          </div>

          <div class="lg:col-span-2">
            <div id="ingest-progress-card" class="hidden bg-white rounded-2xl shadow-xl border border-gray-200 p-8 mb-8">
               <div class="flex items-center justify-between mb-8">
                  <div>
                    <h3 id="ingest-file-title" class="text-lg font-black text-gray-900 truncate max-w-md">Memproses Dokumen...</h3>
                    <p id="ingest-status-text" class="text-sm text-blue-600 font-bold flex items-center gap-2">
                      <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                      </span>
                      Menghubungkan ke Gemini AI...
                    </p>
                  </div>
                  <div class="text-right">
                    <span id="ingest-percentage" class="text-4xl font-black text-gray-900">0%</span>
                  </div>
               </div>

               <!-- Progress Bar -->
               <div class="w-full h-6 bg-gray-100 rounded-full overflow-hidden mb-6 p-1 border border-gray-200">
                  <div id="ingest-progress-bar" class="h-full bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full transition-all duration-1000 ease-out shadow-inner relative" style="width: 0%">
                    <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                  </div>
               </div>

               <div class="grid grid-cols-2 gap-4">
                 <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                   <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Halaman Terproses</p>
                   <p id="ingest-pages-info" class="text-xl font-black text-gray-900">0 / 0</p>
                 </div>
                 <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                   <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Estimasi Selesai</p>
                   <p id="ingest-eta" class="text-xl font-black text-gray-900">Menghitung...</p>
                 </div>
               </div>

               <!-- Cancel Ingest Button -->
               <div class="mt-6 flex justify-end">
                 <button type="button" id="btn-cancel-ingest" onclick="cancelActiveIngestion()" class="px-6 py-2.5 bg-gray-50 hover:bg-red-50 text-gray-500 hover:text-red-600 border border-gray-200 hover:border-red-200 font-bold rounded-xl transition-all text-sm cursor-pointer flex items-center gap-2">
                   <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                   Batalkan Ingest
                 </button>
               </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
               <div class="p-6 border-b border-gray-200 bg-gray-50">
                 <h3 class="font-black text-gray-900">Riwayat Ingest</h3>
               </div>
               <div class="w-full overflow-x-auto">
                 <table class="w-full text-left">
                   <thead class="bg-white border-b border-gray-100">
                     <tr>
                       <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama File</th>
                       <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                       <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Tanggal</th>
                       <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                     </tr>
                   </thead>
                   <tbody id="ingest-history-body" class="divide-y divide-gray-50">
                     @php $ingestions = \App\Models\DocumentIngestion::latest()->get(); @endphp
                     @forelse($ingestions as $ing)
                     <tr class="hover:bg-gray-50 transition-colors">
                       <td class="px-6 py-4">
                         <div class="font-bold text-gray-900 text-sm truncate max-w-xs">{{ $ing->original_name }}</div>
                         <div class="text-[10px] text-gray-400 font-bold uppercase">{{ $ing->processed_pages }} / {{ $ing->total_pages }} Halaman</div>
                       </td>
                       <td class="px-6 py-4 text-center">
                         @if($ing->status === 'completed')
                           <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-black rounded-lg uppercase tracking-wider">Berhasil</span>
                         @elseif($ing->status === 'failed')
                           <span class="px-2 py-1 bg-red-100 text-red-700 text-[10px] font-black rounded-lg uppercase tracking-wider" title="{{ $ing->error_message }}">Gagal</span>
                         @elseif($ing->status === 'cancelled')
                           <span class="px-2 py-1 bg-gray-100 text-gray-700 text-[10px] font-black rounded-lg uppercase tracking-wider">Batal</span>
                         @else
                           <span class="px-2 py-1 bg-blue-100 text-blue-700 text-[10px] font-black rounded-lg uppercase tracking-wider animate-pulse">Proses</span>
                         @endif
                       </td>
                       <td class="px-6 py-4 text-center text-xs text-gray-500 font-medium">
                         {{ $ing->created_at->format('d/m/Y H:i') }}
                       </td>
                       <td class="px-6 py-4 text-right">
                         @if($ing->status !== 'processing' && $ing->status !== 'pending')
                           <button type="button" onclick="deleteIngestion({{ $ing->id }}, '{{ addslashes($ing->original_name) }}')" class="text-xs px-2.5 py-1 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-colors cursor-pointer font-bold">
                             Hapus
                           </button>
                         @endif
                       </td>
                     </tr>
                     @empty
                     <tr>
                       <td colspan="4" class="px-6 py-12 text-center text-gray-400 font-bold">Belum ada riwayat ingest.</td>
                     </tr>
                     @endforelse
                   </tbody>
               </div>
               {{-- Pagination Ingest --}}
               <div id="ingest-pagination" class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50/50"></div>
            </div>
          </div>
        </div>
      </section>

<style>
    .btn-ingest.loading .spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
        margin-right: 10px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .btn-ingest.loading {
        opacity: 0.8;
        cursor: not-allowed;
    }
</style>
@endsection

@push('scripts')
<script>
  // ===== ICON PICKER LOGIC =====
  const HERO_ICONS = [
    { name: 'Rumah', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>' },
    { name: 'Gedung', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>' },
    { name: 'Pendidikan', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm0 0V20"/></svg>' },
    { name: 'Statistik', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>' },
    { name: 'Kesehatan', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>' },
    { name: 'Global', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>' },
    { name: 'Ide/Inovasi', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>' },
    { name: 'Transportasi', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>' },
    { name: 'Pengguna', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>' },
    { name: 'Grup', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>' },
    { name: 'Pekerjaan', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>' },
    { name: 'Cuaca', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>' },
    { name: 'Energi', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>' },
    { name: 'Tulis', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>' },
    { name: 'Pengaturan', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' },
    { name: 'Keamanan', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>' },
  ];

  function toggleIconPicker(type) {
    const dropdown = document.getElementById(`sector-icon-${type}-dropdown`);
    dropdown.classList.toggle('hidden');
  }

  function selectIcon(type, svg, name) {
    document.getElementById(`sector-icon-${type}-value`).value = svg;
    document.getElementById(`sector-icon-${type}-preview`).innerHTML = svg;
    document.getElementById(`sector-icon-${type}-label`).textContent = name;
    document.getElementById(`sector-icon-${type}-label`).classList.remove('text-gray-400');
    document.getElementById(`sector-icon-${type}-label`).classList.add('text-gray-900');
    document.getElementById(`sector-icon-${type}-dropdown`).classList.add('hidden');
  }

  function initIconPickers() {
    ['add', 'edit'].forEach(type => {
      const grid = document.getElementById(`sector-icon-${type}-grid`);
      if (!grid) return;
      HERO_ICONS.forEach(icon => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'p-3 flex items-center justify-center rounded-xl hover:bg-indigo-50 hover:text-indigo-600 border border-gray-100 transition-all';
        btn.innerHTML = icon.svg;
        btn.onclick = () => selectIcon(type, icon.svg, icon.name);
        grid.appendChild(btn);
      });
    });
  }

  document.addEventListener('DOMContentLoaded', initIconPickers);

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
    
    const svg = atob(base64Icon);
    document.getElementById('sector-icon-edit-value').value = svg;
    document.getElementById('sector-icon-edit-preview').innerHTML = svg;
    document.getElementById('sector-icon-edit-label').textContent = 'Icon Terpilih';
    document.getElementById('sector-icon-edit-label').classList.remove('text-gray-400');
    document.getElementById('sector-icon-edit-label').classList.add('text-gray-900');
    
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

  // ==========================================
  // REAL-TIME BACKGROUND POLLING (OPSI A)
  // Menarik data server diam-diam tanpa reload F5
  // ==========================================
  setInterval(() => {
    // Cegah polling mengganggu jika admin sedang mengetik/fokus di dalam salah satu Form input
    const activeTagName = document.activeElement ? document.activeElement.tagName : '';
    if (['INPUT', 'TEXTAREA', 'SELECT'].includes(activeTagName)) return;

    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-Silent-Polling': 'true' } })
    .then(res => res.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // 1. Ekstrak & perbarui 4 Kotak Statistik Atas
        const currentStats = document.querySelector('.grid.grid-cols-2.lg\\:grid-cols-4.gap-6.mb-8');
        const newStats = doc.querySelector('.grid.grid-cols-2.lg\\:grid-cols-4.gap-6.mb-8');
        if (currentStats && newStats) {
            currentStats.innerHTML = newStats.innerHTML;
        }

        // 2. Ekstrak & perbarui Log Aktivitas Admin
        const h2LogsCurr = Array.from(document.querySelectorAll('h2')).find(el => el.textContent.includes('Log Aktivitas'));
        const h2LogsNew  = Array.from(doc.querySelectorAll('h2')).find(el => el.textContent.includes('Log Aktivitas'));
        if (h2LogsCurr && h2LogsNew && h2LogsCurr.nextElementSibling && h2LogsNew.nextElementSibling) {
            h2LogsCurr.nextElementSibling.innerHTML = h2LogsNew.nextElementSibling.innerHTML;
        }

        // 3. Ekstrak & perbarui Tabel Pesan Warga (Kontak)
        const h1MsgsCurr = Array.from(document.querySelectorAll('h1')).find(el => el.textContent.includes('Tiket Aspirasi'));
        const h1MsgsNew  = Array.from(doc.querySelectorAll('h1')).find(el => el.textContent.includes('Tiket Aspirasi'));
        if (h1MsgsCurr && h1MsgsNew) {
            // NextElementSibling dua kali untuk melewati tag <p> deskripsi
            if(h1MsgsCurr.nextElementSibling && h1MsgsCurr.nextElementSibling.nextElementSibling) {
                h1MsgsCurr.nextElementSibling.nextElementSibling.innerHTML = h1MsgsNew.nextElementSibling.nextElementSibling.innerHTML;
            }
        }
        
        // 4. Ekstrak & perbarui Tabel Berita
        const currentNews = document.querySelector('#section-berita table tbody');
        const newNews = doc.querySelector('#section-berita table tbody');
        if (currentNews && newNews) {
            currentNews.innerHTML = newNews.innerHTML;
        }
    }).catch(err => {
        // Abaikan error net/connection silently agar tidak memenuhi console
    });
  }, 15000); // Sinkronisasi setiap 15 detik

  // ===== PROFILE EDITOR: character counters =====
  function updateCounter(editorId, counterId) {
    const el = document.getElementById(editorId);
    const counter = document.getElementById(counterId);
    if (el && counter) {
      counter.textContent = el.value.length + ' karakter';
      el.addEventListener('input', () => counter.textContent = el.value.length + ' karakter');
    }
  }
  updateCounter('editor-sejarah', 'count-sejarah');
  updateCounter('editor-visi', 'count-visi');
  updateCounter('editor-misi', 'count-misi');

  const misiEl = document.getElementById('editor-misi');
  const misiButirEl = document.getElementById('count-misi-butir');
  if (misiEl && misiButirEl) {
    function updateMisiButir() {
      const butir = misiEl.value.split('|').filter(s => s.trim().length > 0).length;
      misiButirEl.textContent = butir + ' butir misi terdeteksi';
    }
    updateMisiButir();
    misiEl.addEventListener('input', updateMisiButir);
  }

  // ===== INGEST CHATBOT LOGIC =====
  const ingestForm = document.getElementById('ingest-form');
  const pdfInput = document.getElementById('pdf_file');
  const filePreview = document.getElementById('file-name-preview');
  const ingestProgressCard = document.getElementById('ingest-progress-card');
  const btnIngest = document.getElementById('btn-start-ingest');
  const btnText = document.getElementById('btn-text');
  const spinner = btnIngest ? btnIngest.querySelector('.spinner') : null;
  let pollingInterval = null;
  let activeIngestionId = null;

  pdfInput?.addEventListener('change', function() {
    if (this.files && this.files[0]) {
      filePreview.classList.remove('hidden');
      filePreview.querySelector('span').textContent = this.files[0].name;
    }
  });

  ingestForm?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    if (btnIngest) {
      btnIngest.disabled = true;
      btnIngest.classList.add('loading');
    }
    if (spinner) spinner.classList.remove('hidden');
    if (btnText) btnText.innerText = 'Memulai...';

    try {
      const response = await fetch("{{ route('admin.chatbot.ingest') }}", {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
      });
      const data = await response.json();
      if (data.success) {
        startPolling(data.ingestion_id, formData.get('pdf_file').name);
        Swal.fire({ icon: 'success', title: 'Ingest Dimulai', text: data.message, timer: 2000, showConfirmButton: false });
      } else {
        throw new Error(data.message);
      }
    } catch (error) {
      console.error('Error:', error);
      Swal.fire('Error', error.message || 'Gagal memulai ingest', 'error');
      resetIngestButton();
    }
  });

  function resetIngestButton() {
    if (btnIngest) {
        btnIngest.disabled = false;
        btnIngest.classList.remove('loading');
        if (spinner) spinner.classList.add('hidden');
        if (btnText) btnText.innerText = 'Mulai Proses Ingest';
    }
  }

  function startPolling(id, fileName) {
    if (ingestProgressCard) ingestProgressCard.classList.remove('hidden');
    const fileTitle = document.getElementById('ingest-file-title');
    if (fileTitle) fileTitle.textContent = fileName;
    if (btnIngest) btnIngest.disabled = true;
    
    activeIngestionId = id;
    if (pollingInterval) clearInterval(pollingInterval);
    
    pollingInterval = setInterval(async () => {
      try {
        const res = await fetch(`/admin/chatbot/ingest-status/${id}`);
        const data = await res.json();
        
        updateIngestUI(data);
        
        if (data.status === 'completed' || data.status === 'failed' || data.status === 'cancelled') {
          clearInterval(pollingInterval);
          if (data.status === 'completed') {
            Swal.fire({
                icon: 'success',
                title: 'Selesai!',
                text: 'Dokumen berhasil di-ingest sepenuhnya.',
                confirmButtonColor: '#2563eb'
            }).then(() => {
                location.hash = '#section-ingest';
                location.reload();
            });
          } else if (data.status === 'cancelled') {
            Swal.fire('Batal', 'Proses ingest telah dibatalkan.', 'info').then(() => {
                location.hash = '#section-ingest';
                location.reload();
            });
          } else {
            Swal.fire('Gagal', 'Terjadi kesalahan: ' + data.error, 'error');
            resetIngestButton();
          }
        }
      } catch (e) {
        console.error('Polling error:', e);
      }
    }, 2000);
  }

  window.cancelActiveIngestion = async function() {
    if (!activeIngestionId) return;
    
    const confirmCancel = await Swal.fire({
      title: 'Batalkan Ingest?',
      text: 'Proses pemrosesan dokumen PDF akan dihentikan.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, Batalkan!',
      cancelButtonText: 'Tidak'
    });

    if (confirmCancel.isConfirmed) {
      try {
        const response = await fetch(`/admin/chatbot/ingest/${activeIngestionId}/cancel`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
          }
        });
        const data = await response.json();
        if (data.success) {
          if (pollingInterval) clearInterval(pollingInterval);
          Swal.fire('Dibatalkan', data.message, 'success').then(() => {
            location.hash = '#section-ingest';
            location.reload();
          });
        } else {
          Swal.fire('Gagal', data.message, 'error');
        }
      } catch (error) {
        console.error('Error canceling ingest:', error);
        Swal.fire('Error', 'Gagal membatalkan proses ingest.', 'error');
      }
    }
  };

  window.deleteIngestion = async function(id, fileName) {
    const confirmDelete = await Swal.fire({
      title: 'Hapus Dokumen?',
      text: `Apakah Anda yakin ingin menghapus dokumen "${fileName}"? AI tidak akan lagi menjawab pertanyaan dari file ini.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Batal'
    });

    if (confirmDelete.isConfirmed) {
      try {
        const response = await fetch(`/admin/chatbot/ingest/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
          }
        });
        const data = await response.json();
        if (data.success) {
          Swal.fire('Terhapus!', data.message, 'success').then(() => {
            location.hash = '#section-ingest';
            location.reload();
          });
        } else {
          Swal.fire('Gagal', data.message, 'error');
        }
      } catch (error) {
        console.error('Error deleting ingestion:', error);
        Swal.fire('Error', 'Gagal menghapus dokumen.', 'error');
      }
    }
  };

  function updateIngestUI(data) {
    const bar = document.getElementById('ingest-progress-bar');
    const pct = document.getElementById('ingest-percentage');
    const info = document.getElementById('ingest-pages-info');
    const statusText = document.getElementById('ingest-status-text');
    const eta = document.getElementById('ingest-eta');

    bar.style.width = data.progress + '%';
    pct.textContent = data.progress + '%';
    info.textContent = `${data.processed} / ${data.total} Halaman`;
    
    if (data.status === 'processing') {
      statusText.innerHTML = `
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
        </span>
        Sedang memproses halaman ${data.processed + 1}...
      `;
      
      if (data.estimated_seconds > 0) {
        const mins = Math.floor(data.estimated_seconds / 60);
        const secs = data.estimated_seconds % 60;
        eta.textContent = mins > 0 ? `± ${mins}m ${secs}d` : `± ${secs} detik`;
      } else {
        eta.textContent = 'Menghitung...';
      }
    } else if (data.status === 'completed') {
      statusText.textContent = '✅ Selesai!';
      statusText.className = 'text-sm text-green-600 font-bold';
      eta.textContent = 'Selesai';
    } else if (data.status === 'failed') {
      statusText.textContent = '❌ Gagal';
      statusText.className = 'text-sm text-red-600 font-bold';
      eta.textContent = '-';
    }
  }

  // ===== SETELAN MODEL AI LOGIC =====
  window.selectModel = function(modelName) {
    const input = document.getElementById('gemini_model');
    if (!input) return;
    input.value = modelName;
    document.getElementById('active-model-display').innerText = modelName;
    
    document.querySelectorAll('.model-badge').forEach(btn => {
      if (btn.getAttribute('onclick').includes(`'${modelName}'`)) {
        btn.className = "model-badge px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all duration-200 bg-blue-600 border-blue-600 text-white shadow-md";
      } else {
        btn.className = "model-badge px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all duration-200 bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100 hover:border-gray-300";
      }
    });
  };

  document.getElementById('gemini_model')?.addEventListener('input', function(e) {
    const val = e.target.value.trim();
    document.getElementById('active-model-display').innerText = val ? val : 'Tidak diset';
    
    document.querySelectorAll('.model-badge').forEach(btn => {
      if (btn.getAttribute('onclick').includes(`'${val}'`)) {
        btn.className = "model-badge px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all duration-200 bg-blue-600 border-blue-600 text-white shadow-md";
      } else {
        btn.className = "model-badge px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all duration-200 bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100 hover:border-gray-300";
      }
    });
  });

  // ===== SEARCH + FILTER + PAGINATION ENGINE =====
  (function() {
    const DEFAULT_PER_PAGE = 5;

    /* ---- Shared: build pagination bar HTML ---- */
    function buildPaginationHTML(currentPage, totalPages, start, end, total) {
      if (totalPages <= 1) return '';
      const btnBase     = 'inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold transition-all duration-150';
      const btnActive   = 'bg-blue-600 text-white shadow-sm';
      const btnInactive = 'bg-white border border-gray-200 text-gray-600 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200';
      const btnDisabled = 'bg-gray-50 border border-gray-100 text-gray-300 cursor-not-allowed';
      let pages = '';
      let startPg = Math.max(1, currentPage - 2);
      let endPg   = Math.min(totalPages, startPg + 4);
      if (endPg - startPg < 4) startPg = Math.max(1, endPg - 4);
      for (let p = startPg; p <= endPg; p++) {
        pages += `<button data-pg="${p}" class="${btnBase} ${p === currentPage ? btnActive : btnInactive}">${p}</button>`;
      }
      const prevDis = currentPage === 1;
      const nextDis = currentPage === totalPages;
      const info = total === 0
        ? `<span class="text-xs text-gray-400 font-medium">Tidak ada data ditemukan</span>`
        : `<span class="text-xs text-gray-400 font-medium">Menampilkan <span class="font-bold text-gray-700">${start}–${end}</span> dari <span class="font-bold text-gray-700">${total}</span> data</span>`;
      return `
        ${info}
        <div class="flex items-center gap-1">
          <button data-pg="${currentPage-1}" ${prevDis?'disabled':''} class="${btnBase} ${prevDis?btnDisabled:btnInactive}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
          </button>
          ${pages}
          <button data-pg="${currentPage+1}" ${nextDis?'disabled':''} class="${btnBase} ${nextDis?btnDisabled:btnInactive}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>`;
    }

    /* ---- Generic row paginator that operates on a visible subset ---- */
    function createRowPaginator(tbodyId, paginationId, getVisibleRows, perPage = DEFAULT_PER_PAGE) {
      const tbody = document.getElementById(tbodyId);
      const paginationEl = document.getElementById(paginationId);
      if (!tbody || !paginationEl) return { refresh: ()=>{} };
      let currentPage = 1;

      function refresh() {
        const visible = getVisibleRows();
        const total   = visible.length;
        const totalPages = Math.max(1, Math.ceil(total / perPage));
        if (currentPage > totalPages) currentPage = 1;
        // hide all rows first
        Array.from(tbody.querySelectorAll('tr')).forEach(r => r.style.display = 'none');
        // show slice
        visible.forEach((row, i) => {
          row.style.display = (i >= (currentPage-1)*perPage && i < currentPage*perPage) ? '' : 'none';
        });
        const start = total === 0 ? 0 : (currentPage-1)*perPage + 1;
        const end   = Math.min(currentPage*perPage, total);
        paginationEl.innerHTML = buildPaginationHTML(currentPage, totalPages, start, end, total);
      }

      paginationEl.addEventListener('click', e => {
        const btn = e.target.closest('[data-pg]');
        if (!btn || btn.disabled) return;
        const pg = parseInt(btn.dataset.pg);
        if (!isNaN(pg)) { currentPage = pg; refresh(); }
      });

      return { refresh: () => { currentPage = 1; refresh(); }, go: () => refresh() };
    }

    /* ---- Generic card paginator that operates on a visible subset ---- */
    function createCardPaginator(gridId, paginationId, getVisibleCards, perPage = DEFAULT_PER_PAGE) {
      const grid = document.getElementById(gridId);
      const paginationEl = document.getElementById(paginationId);
      if (!grid || !paginationEl) return { refresh: ()=>{} };
      let currentPage = 1;
      const allCards = Array.from(grid.querySelectorAll(':scope > div'));

      function refresh() {
        const visible = getVisibleCards();
        const total   = visible.length;
        const totalPages = Math.max(1, Math.ceil(total / perPage));
        if (currentPage > totalPages) currentPage = 1;
        allCards.forEach(c => c.style.display = 'none');
        visible.forEach((card, i) => {
          card.style.display = (i >= (currentPage-1)*perPage && i < currentPage*perPage) ? '' : 'none';
        });
        const start = total === 0 ? 0 : (currentPage-1)*perPage + 1;
        const end   = Math.min(currentPage*perPage, total);
        paginationEl.innerHTML = buildPaginationHTML(currentPage, totalPages, start, end, total);
      }

      paginationEl.addEventListener('click', e => {
        const btn = e.target.closest('[data-pg]');
        if (!btn || btn.disabled) return;
        const pg = parseInt(btn.dataset.pg);
        if (!isNaN(pg)) { currentPage = pg; refresh(); }
      });

      return { refresh: () => { currentPage = 1; refresh(); }, go: () => refresh() };
    }

    document.addEventListener('DOMContentLoaded', function() {

      /* ======================================================
         1. BERITA — search by title, filter status & kategori
         ====================================================== */
      (function() {
        const tbody      = document.getElementById('berita-tbody');
        if (!tbody) return;
        const allRows    = Array.from(tbody.querySelectorAll('tr'));
        const searchEl   = document.getElementById('berita-search');
        const statusEl   = document.getElementById('berita-filter-status');
        const kategoriEl = document.getElementById('berita-filter-kategori');

        function getVisible() {
          const q   = (searchEl?.value  || '').toLowerCase().trim();
          const st  = (statusEl?.value  || '').toLowerCase().trim();
          const kat = (kategoriEl?.value || '').toLowerCase().trim();
          return allRows.filter(row => {
            const title   = (row.querySelector('td:nth-child(2) .font-bold')?.textContent || '').toLowerCase();
            const katCell = (row.querySelector('td:nth-child(3)')?.textContent || '').toLowerCase().trim();
            // status badge text: "Publik" or "Draft"
            const statusBadge = (row.querySelector('td:nth-child(4) span')?.textContent || '').toLowerCase().trim();
            const matchQ   = !q   || title.includes(q);
            const matchSt  = !st  || statusBadge.includes(st);
            const matchKat = !kat || katCell.includes(kat);
            return matchQ && matchSt && matchKat;
          });
        }

        const paginator = createRowPaginator('berita-tbody', 'berita-pagination', getVisible);
        paginator.refresh();

        [searchEl, statusEl, kategoriEl].forEach(el => {
          el?.addEventListener('input',  () => paginator.refresh());
          el?.addEventListener('change', () => paginator.refresh());
        });
      })();

      /* ======================================================
         2. PENGGUNA — simple pagination (no search needed here)
         ====================================================== */
      (function() {
        const tbody = document.getElementById('pengguna-tbody');
        const paginationEl = document.getElementById('pengguna-pagination');
        if (!tbody || !paginationEl) return;
        const allRows = Array.from(tbody.querySelectorAll('tr'));
        let currentPage = 1;

        function refresh() {
          const total = allRows.length;
          const totalPages = Math.max(1, Math.ceil(total / DEFAULT_PER_PAGE));
          if (currentPage > totalPages) currentPage = 1;
          allRows.forEach((row, i) => {
            row.style.display = (i >= (currentPage-1)*DEFAULT_PER_PAGE && i < currentPage*DEFAULT_PER_PAGE) ? '' : 'none';
          });
          const start = total === 0 ? 0 : (currentPage-1)*DEFAULT_PER_PAGE + 1;
          const end   = Math.min(currentPage*DEFAULT_PER_PAGE, total);
          paginationEl.innerHTML = buildPaginationHTML(currentPage, totalPages, start, end, total);
        }
        paginationEl.addEventListener('click', e => {
          const btn = e.target.closest('[data-pg]');
          if (!btn || btn.disabled) return;
          const pg = parseInt(btn.dataset.pg);
          if (!isNaN(pg)) { currentPage = pg; refresh(); }
        });
        refresh();
      })();

      /* ======================================================
         3. ASPIRASI — search by name/subject, filter by status
         ====================================================== */
      (function() {
        const grid = document.getElementById('aspirasi-grid');
        if (!grid) return;
        const allCards  = Array.from(grid.querySelectorAll(':scope > div'));
        const searchEl  = document.getElementById('aspirasi-search');
        let activeFilter = 'all'; // State filter yang sesungguhnya

        function getVisible() {
          const q = (searchEl?.value || '').toLowerCase().trim();
          return allCards.filter(card => {
            const text   = card.textContent.toLowerCase();
            // Sangat akurat: badge status selesai memiliki bg-green-100 di class-nya
            const isResolved = card.querySelector('.bg-green-100') !== null;
            
            const matchQ = !q || text.includes(q);
            const matchF = activeFilter === 'all'
              || (activeFilter === 'resolved' && isResolved)
              || (activeFilter === 'unread'   && !isResolved);
            return matchQ && matchF;
          });
        }

        const paginator = createCardPaginator('aspirasi-grid', 'aspirasi-pagination', getVisible, DEFAULT_PER_PAGE);
        paginator.refresh();

        searchEl?.addEventListener('input', () => paginator.refresh());

        // Override the global function so it shares scope with the paginator
        window.filterAspirasi = function(type) {
          activeFilter = type; // Update the real state
          
          // Update button styles
          document.querySelectorAll('.aspirasi-filter-btn').forEach(btn => {
            btn.className = 'aspirasi-filter-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all text-gray-500 hover:text-gray-700';
          });
          const activeBtn = document.getElementById(`aspirasi-filter-${type}`);
          if (activeBtn) {
            activeBtn.className = 'aspirasi-filter-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all bg-white text-blue-600 shadow-sm';
          }

          // Let the paginator engine handle everything else
          paginator.refresh();
        };
      })();

      /* ======================================================
         4. CAPAIAN — search across sector name & indicator names
         ====================================================== */
      (function() {
        const grid = document.getElementById('capaian-grid');
        if (!grid) return;
        const allCards = Array.from(grid.querySelectorAll(':scope > div'));
        const searchEl = document.getElementById('capaian-search');

        function getVisible() {
          const q = (searchEl?.value || '').toLowerCase().trim();
          if (!q) return allCards;
          return allCards.filter(card => {
            // Sector name
            const sectorName = (card.querySelector('h2')?.textContent || '').toLowerCase();
            // Indicator names inside tbody tds
            const indicators = Array.from(card.querySelectorAll('tbody td:first-child'))
              .map(td => td.textContent.toLowerCase());
            return sectorName.includes(q) || indicators.some(ind => ind.includes(q));
          });
        }

        const paginator = createCardPaginator('capaian-grid', 'capaian-pagination', getVisible);
        paginator.refresh();

        searchEl?.addEventListener('input', () => paginator.refresh());
      })();

      /* ======================================================
         5. INGEST HISTORY — simple pagination (10 per page)
         ====================================================== */
      (function() {
        const tbody = document.getElementById('ingest-history-body');
        if (!tbody) return;
        const allRows = Array.from(tbody.querySelectorAll('tr'));
        const paginator = createRowPaginator('ingest-history-body', 'ingest-pagination', () => allRows, 10);
        paginator.refresh();
      })();

      /* ======================================================
         6. ACTIVITY LOG — date filter (scrollable list)
         ====================================================== */
      // Didefinisikan di global agar bisa dipanggil via onchange inline
      window.filterActivityByDate = function(selectedDate) {
        const activityItems = document.querySelectorAll('.activity-item');
        if (!activityItems.length) return;
        
        activityItems.forEach(item => {
          if (!selectedDate) {
            // Jika kosong, pastikan terlihat
            item.style.setProperty('display', 'flex', 'important');
          } else {
            // Bandingkan
            if (item.getAttribute('data-date') === selectedDate) {
              item.style.setProperty('display', 'flex', 'important');
            } else {
              item.style.setProperty('display', 'none', 'important');
            }
          }
        });
      };


    }); // end DOMContentLoaded
  })();

</script>

<script>
function filterActivityByDate(selectedDate) {
    // Ambil semua elemen activity item
    const activityItems = document.querySelectorAll('.activity-item');
    // Ambil elemen pesan kosong jika ada (opsional)
    const emptyState = document.getElementById('activity-empty-state');
    
    let visibleCount = 0;

    activityItems.forEach(item => {
        // Ambil data-date dari elemen HTML (format: Y-m-d)
        const itemDate = item.getAttribute('data-date');

        // Jika input dikosongkan (clear), tampilkan semua data.
        // Jika tidak kosong, cocokkan dengan data-date
        if (!selectedDate || itemDate === selectedDate) {
            item.style.display = 'flex'; // Tampilkan kembali
            visibleCount++;
        } else {
            item.style.display = 'none'; // Sembunyikan
        }
    });

    // Menampilkan state kosong jika setelah difilter tidak ada data yang cocok
    if (emptyState) {
        if (visibleCount === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }
    }
}
</script>
@endpush
