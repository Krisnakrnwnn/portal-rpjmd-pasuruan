@extends('layouts.admin')
@section('title', $pageTitle . ' - Bapperida Admin')
@section('content')
<section id="section-berita" class="content-section block">
  <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
    <div>
      <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 tracking-tight">Manajemen Berita</h1>
      <p class="text-sm text-slate-500 mt-1">Mengelola publikasi berita harian dan artikel publik RPJMD.</p>
    </div>
    <a href="{{ route('admin.berita.create', request()->only(['q', 'status', 'category', 'category_id', 'page'])) }}" class="inline-flex items-center gap-2 bg-gray-900 hover:bg-black text-white font-semibold py-2.5 px-5 rounded-xl shadow-sm cursor-pointer transition-colors text-sm focus-visible:outline-2 focus-visible:outline-gray-900" aria-label="Tambah Berita">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
      Tambah Berita
    </a>
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-4 lg:p-6 border-b border-slate-200 flex flex-wrap gap-4 items-center justify-between bg-slate-50/50">
      <div>
        <h2 class="text-lg font-semibold text-slate-900">Daftar Publikasi Harian</h2>
        <p class="text-xs text-slate-500">Filter dan kelola status berita publik atau draft.</p>
      </div>
      <div class="flex flex-wrap gap-3 items-center">
        {{-- Filter Status --}}
        <select id="berita-filter-status" data-admin-filter="status" aria-label="Filter status berita" class="text-sm border border-slate-300 rounded-xl px-3.5 py-2 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 text-slate-700 transition-all bg-white font-medium">
          <option value="">Semua Status</option>
          <option value="publik">Publik</option>
          <option value="draft">Draft</option>
        </select>
        {{-- Filter Kategori --}}
        <select id="berita-filter-kategori" data-admin-filter="category" aria-label="Filter kategori berita" class="text-sm border border-slate-300 rounded-xl px-3.5 py-2 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 text-slate-700 transition-all bg-white font-medium">
          <option value="">Semua Kategori</option>
          @foreach($newsCategories as $cat)
            <option value="{{ strtolower($cat) }}">{{ $cat }}</option>
          @endforeach
        </select>
      </div>
    </div>
    
    <div class="w-full overflow-x-auto">
      <table class="w-full min-w-[700px] text-left border-collapse">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="px-5 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider w-16">Gambar</th>
            <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">Judul Artikel</th>
            <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">Kategori</th>
            <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Aksi</th>
          </tr>
        </thead>
        <tbody id="berita-tbody" data-admin-live="berita-tbody" class="divide-y divide-slate-100 whitespace-nowrap text-sm">
          @forelse($news as $post)
          <tr class="hover:bg-slate-50/80 transition-colors">
            {{-- Thumbnail --}}
            <td class="px-5 py-4">
              @if($post->image_url)
                <img src="{{ asset($post->image_url) }}" alt="{{ $post->title }}"
                  class="w-12 h-12 object-cover rounded-xl border border-slate-200 shadow-sm">
              @else
                <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
              @endif
            </td>
            <td class="px-6 py-4">
              <div class="font-semibold text-slate-900 max-w-xs truncate">{{ $post->title }}</div>
              <div class="text-xs text-slate-400 font-medium mt-0.5">{{ $post->created_at->format('d M Y') }}</div>
            </td>
            <td class="px-6 py-4 text-slate-600 font-medium">{{ $post->category }}</td>
            <td class="px-6 py-4">
              @if($post->is_published)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Publik
                </span>
              @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft
                </span>
              @endif
            </td>
            <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
              {{-- Toggle Publik/Draft --}}
              <form action="{{ route('admin.toggle_publish', $post->id) }}{{ \App\Support\AdminNavigation::context() ? '?'.http_build_query(\App\Support\AdminNavigation::context()) : '' }}" method="POST" class="inline toggle-form">
                @csrf
                @if($errors->any())<x-input-error :messages="$errors->all()" class="mb-4" role="alert" />@endif
                <button type="submit"
                  data-title="{{ $post->is_published ? 'Jadikan Draft?' : 'Publikasikan?' }}"
                  data-text="{{ $post->is_published ? 'Berita tidak akan tampil di halaman publik.' : 'Berita akan tampil di halaman publik.' }}"
                  data-confirm="{{ $post->is_published ? 'Ya, Jadikan Draft' : 'Ya, Publikasikan' }}"
                  data-color="{{ $post->is_published ? '#d97706' : '#16a34a' }}"
                  class="toggle-btn text-xs px-3 py-1.5 rounded-xl font-medium transition-colors border
                    {{ $post->is_published ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-600 hover:text-white' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-600 hover:text-white' }}">
                  {{ $post->is_published ? 'Jadikan Draft' : 'Publikasikan' }}
                </button>
              </form>
              <a href="{{ route('admin.berita.edit', array_merge(['news' => $post->id], request()->only(['q', 'status', 'category', 'page']))) }}" class="text-xs px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-xl font-medium hover:bg-blue-600 hover:text-white transition-colors inline-block">Edit</a>
              <form action="{{ route('admin.delete_news', $post->id) }}{{ \App\Support\AdminNavigation::context() ? '?'.http_build_query(\App\Support\AdminNavigation::context()) : '' }}" method="POST" class="inline">
                  @csrf
                  @if($errors->any())<x-input-error :messages="$errors->all()" class="mb-4" role="alert" />@endif
                  @method('DELETE')
                  <button type="button" onclick="confirmDelete(this.closest('form'), 'Hapus berita ini?')" class="text-xs px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 rounded-xl font-medium hover:bg-red-600 hover:text-white transition-colors">Hapus</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">Tidak ada berita yang cocok.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{-- Pagination Berita --}}
    <div data-admin-live="news-pagination"><x-admin.pagination :paginator="$news" /></div>
  </div>
</section>
@endsection
