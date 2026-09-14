@extends('layouts.admin')
@section('title', $pageTitle . ' - Bapperida Admin')
@section('content')
<section id="section-dokumen-form" class="content-section block font-sans">
        <div class="flex items-center gap-4 mb-6">
          <a href="{{ route('admin.dokumen.index', request()->only(['q', 'status', 'category', 'category_id', 'page'])) }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-600" aria-label="Kembali atau buka halaman dokumen">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </a>
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tambah Dokumen Baru</h1>
            <p class="text-slate-500 text-sm">Unggah file PDF dokumen perencanaan atau laporan RPJMD.</p>
          </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8 max-w-2xl">
          <form action="{{ route('admin.store_document') }}{{ \App\Support\AdminNavigation::context() ? '?'.http_build_query(\App\Support\AdminNavigation::context()) : '' }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if($errors->any())<x-input-error :messages="$errors->all()" class="mb-4" role="alert" />@endif
            <div class="space-y-2">
              <label class="text-sm font-semibold text-slate-700">Upload File PDF <span class="text-rose-500">*</span></label>
              <input type="file" name="files[]" multiple accept=".pdf" required onchange="document.getElementById('add-doc-title').value = this.files.length === 1 ? this.files[0].name.replace(/\.[^/.]+$/, '') : ''" class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-semibold text-slate-700">Judul Dokumen</label>
              <input type="text" name="title" id="add-doc-title" placeholder="Opsional (Otomatis pakai nama file jika kosong/upload banyak)" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all text-slate-800 placeholder-slate-400" value="{{ old('title', $formDefaults['title'] ?? '') }}">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-semibold text-slate-700">Kategori <span class="text-rose-500">*</span></label>
              <select name="document_category_id" id="add-doc-category" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all text-slate-800 bg-white">
                @foreach($documentCategories as $cat)
                  <option value="{{ $cat->id }}" @selected((string) old('document_category_id', $formDefaults['document_category_id'] ?? '') === (string) ($cat->id))>
                      {{ $cat->parent ? $cat->parent->name . ' » ' : '' }}{{ $cat->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
              <a href="{{ route('admin.dokumen.index', request()->only(['q', 'status', 'category', 'category_id', 'page'])) }}" class="px-5 py-2.5 rounded-xl font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all text-sm focus:outline-none focus:ring-2 focus:ring-slate-400" aria-label="Kembali atau buka halaman dokumen">Batalkan</a>
              <button type="submit" class="px-6 py-2.5 rounded-xl font-semibold bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition-all text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">Simpan Dokumen</button>
            </div>
          </form>
        </div>
      </section>
@endsection
