@extends('layouts.admin')
@section('title', $pageTitle . ' - Bapperida Admin')
@section('content')
<section id="section-dokumen" class="content-section block">
  <!-- HEADER KATEGORI -->
  <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
      <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 tracking-tight">Bank Data & Dokumen</h1>
      <p class="text-sm text-slate-500 mt-1">Kelola taksonomi kategori dokumen serta berkas resmi RPJMD Kabupaten Pasuruan.</p>
    </div>
  </div>

  <!-- GRID KATEGORI -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
    <!-- Form Tambah Kategori -->
    <div class="lg:col-span-1">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-lg font-semibold text-slate-900 mb-4 border-b border-slate-100 pb-3">Tambah Kategori Baru</h3>
        <form action="{{ \App\Support\AdminNavigation::url('admin.document-categories.store') }}" method="POST" class="space-y-4">
          @csrf
          @if($errors->any())<x-input-error :messages="$errors->all()" class="mb-4" role="alert" />@endif
          <div>
            <label class="text-xs font-semibold text-slate-700 block mb-1">Induk Kategori (Opsional)</label>
            <select name="parent_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all text-sm bg-white font-medium">
              <option value="" @selected((string) old('parent_id', $formDefaults['parent_id'] ?? '') === (string) (''))>-- Tidak Ada (Kategori Utama) --</option>
              @foreach($categoryOptions as $c)
                <option value="{{ $c->id }}" @selected((string) old('parent_id', $formDefaults['parent_id'] ?? '') === (string) ($c->id))>{{ $c->name }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold text-slate-700 block mb-1">Nama Kategori</label>
            <input type="text" name="name" id="kategori-name" required placeholder="Contoh: Laporan Kinerja" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all text-sm font-medium" oninput="document.getElementById('kategori-slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '')" value="{{ old('name', $formDefaults['name'] ?? '') }}">
          </div>
          <div>
            <label class="text-xs font-semibold text-slate-700 block mb-1">Slug (Otomatis)</label>
            <input type="text" name="slug" id="kategori-slug" required readonly placeholder="laporan-kinerja" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 outline-none text-sm text-slate-500 font-medium" value="{{ old('slug', $formDefaults['slug'] ?? '') }}">
          </div>
          <div>
            <label class="text-xs font-semibold text-slate-700 block mb-1">Deskripsi Singkat</label>
            <textarea name="description" rows="3" placeholder="Penjelasan singkat untuk menu..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all text-sm font-medium">{{ old('description', $formDefaults['description'] ?? '') }}</textarea>
          </div>
          <button type="submit" class="w-full py-2.5 rounded-xl font-semibold bg-gray-900 text-white hover:bg-black shadow-sm transition-all text-sm focus-visible:outline-2 focus-visible:outline-gray-900">Simpan Kategori</button>
        </form>
      </div>
    </div>

    <!-- Tabel Kategori -->
    <div class="lg:col-span-2">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50/50">
          <h3 class="text-base font-semibold text-slate-900">Daftar Kategori Dokumen</h3>
        </div>
        <div class="overflow-x-auto overflow-y-auto max-h-[450px]">
          <table class="w-full text-left relative">
            <thead class="bg-slate-50 border-b border-slate-200 sticky top-0 z-10">
              <tr>
                <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">Nama Kategori</th>
                <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">Total Dokumen</th>
                <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Aksi</th>
              </tr>
            </thead>
            <tbody data-admin-live="categories" class="divide-y divide-slate-100 text-sm">
              @forelse($documentCategories as $cat)
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="px-6 py-4">
                  <p class="font-semibold text-slate-900 break-words">
                    @if($cat->parent)
                      <span class="text-slate-400 font-normal">{{ $cat->parent->name }} &raquo;</span>
                    @endif
                    {{ $cat->name }}
                  </p>
                  <p class="text-xs text-slate-400 max-w-xs truncate mt-0.5">{{ $cat->description }}</p>
                </td>
                <td class="px-6 py-4 font-semibold text-slate-900">
                  <span class="bg-slate-100 text-slate-700 border border-slate-200 px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap">{{ $cat->documents_count }} Dokumen</span>
                </td>
                <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                  <a href="{{ route('admin.dokumen.create', ['category_id' => $cat->id]) }}" class="text-xs px-3 py-1.5 bg-slate-100 text-slate-700 border border-slate-200 font-semibold rounded-xl hover:bg-gray-900 hover:text-white transition-colors inline-block">+ Dokumen</a>
                  <form action="{{ route('admin.document-categories.destroy', $cat->id) }}{{ \App\Support\AdminNavigation::context() ? '?'.http_build_query(\App\Support\AdminNavigation::context()) : '' }}" method="POST" class="inline">
                    @csrf
                    @if($errors->any())<x-input-error :messages="$errors->all()" class="mb-4" role="alert" />@endif
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete(this.closest('form'), 'Hapus kategori ini? Pastikan tidak ada dokumen di dalamnya.')" class="text-xs px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 font-semibold rounded-xl hover:bg-red-600 hover:text-white transition-colors">Hapus</button>
                  </form>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="3" class="px-6 py-8 text-center text-slate-400 text-sm font-medium">Belum ada kategori.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div data-admin-live="documentCategories-pagination" class="mb-8"><x-admin.pagination :paginator="$documentCategories" /></div>
  <hr class="border-slate-200 mb-10">

  <!-- HEADER DOKUMEN -->
  <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
    <div>
      <h2 class="text-xl lg:text-2xl font-bold text-slate-900 tracking-tight">Berkas Dokumen Publik</h2>
      <p class="text-sm text-slate-500 mt-0.5">Daftar file PDF resmi (RPJMD, RKPD, Laporan Kinerja, dll.)</p>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto overflow-y-auto max-h-[500px]">
      <table class="w-full text-left relative">
        <thead class="bg-slate-50 border-b border-slate-200 sticky top-0 z-10">
          <tr>
            <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">Judul Dokumen</th>
            <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">Kategori</th>
            <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Aksi</th>
          </tr>
        </thead>
        <tbody data-admin-live="documents" class="divide-y divide-slate-100 whitespace-nowrap text-sm">
          @forelse($publicDocuments as $doc)
          <tr class="hover:bg-slate-50/80 transition-colors">
            <td class="px-6 py-4 font-semibold text-slate-900 max-w-xs truncate">{{ $doc->title }}</td>
            <td class="px-6 py-4 text-slate-600 font-medium">{{ $doc->documentCategory->name ?? $doc->category }}</td>
            <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
              <a href="{{ route('admin.dokumen.edit', array_merge(['document' => $doc->id], request()->only(['q', 'category_id', 'page']))) }}" class="text-xs px-3 py-1.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-xl font-semibold hover:bg-gray-900 hover:text-white transition-colors inline-block">Edit</a>
              <form action="{{ route('admin.delete_document', $doc->id) }}{{ \App\Support\AdminNavigation::context() ? '?'.http_build_query(\App\Support\AdminNavigation::context()) : '' }}" method="POST" class="inline">
                @csrf
                @if($errors->any())<x-input-error :messages="$errors->all()" class="mb-4" role="alert" />@endif
                @method('DELETE')
                <button type="button" onclick="confirmDelete(this.closest('form'), 'Hapus dokumen ini?')" class="text-xs px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 rounded-xl font-semibold hover:bg-red-600 hover:text-white transition-colors">Hapus</button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="3" class="px-6 py-12 text-center text-slate-400 text-sm">Belum ada dokumen.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div data-admin-live="publicDocuments-pagination" class="mt-4"><x-admin.pagination :paginator="$publicDocuments" /></div>
</section>
@endsection
