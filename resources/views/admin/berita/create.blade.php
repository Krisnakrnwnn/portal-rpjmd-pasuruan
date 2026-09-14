@extends('layouts.admin')
@section('title', $pageTitle . ' - Bapperida Admin')
@section('content')
<section id="section-berita-form" class="content-section block font-sans">
        <div class="flex items-center gap-4 mb-6">
          <a href="{{ route('admin.berita.index', request()->only(['q', 'status', 'category', 'category_id', 'page'])) }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-600" aria-label="Kembali atau buka halaman berita">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </a>
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tulis Berita Baru</h1>
            <p class="text-slate-500 text-sm">Lengkapi formulir di bawah ini untuk menerbitkan berita RPJMD.</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8 max-w-4xl">
          <form action="{{ route('admin.store_news') }}{{ \App\Support\AdminNavigation::context() ? '?'.http_build_query(\App\Support\AdminNavigation::context()) : '' }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if($errors->any())<x-input-error :messages="$errors->all()" class="mb-4" role="alert" />@endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Judul Artikel <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required placeholder="Masukkan judul menarik..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all text-slate-800 placeholder-slate-400" value="{{ old('title', $formDefaults['title'] ?? '') }}">
              </div>
              <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Kategori <span class="text-rose-500">*</span></label>
                <select name="category" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all text-slate-800 bg-white">
                  <option value="Musrenbang" @selected((string) old('category', $formDefaults['category'] ?? '') === (string) ('Musrenbang'))>Musrenbang</option>
                  <option value="Infrastruktur" @selected((string) old('category', $formDefaults['category'] ?? '') === (string) ('Infrastruktur'))>Infrastruktur</option>
                  <option value="Kesehatan" @selected((string) old('category', $formDefaults['category'] ?? '') === (string) ('Kesehatan'))>Kesehatan</option>
                  <option value="Lingkungan" @selected((string) old('category', $formDefaults['category'] ?? '') === (string) ('Lingkungan'))>Lingkungan</option>
                  <option value="Dokumen Resmi" @selected((string) old('category', $formDefaults['category'] ?? '') === (string) ('Dokumen Resmi'))>Dokumen Resmi</option>
                </select>
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-semibold text-slate-700">Gambar Artikel</label>
              <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
              <p class="text-xs text-slate-500 font-medium">Format yang didukung: JPG, PNG, GIF. Ukuran maksimal: 2 MB.</p>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-semibold text-slate-700">Konten Berita <span class="text-rose-500">*</span></label>
              <textarea name="content" required rows="8" placeholder="Tulis rincian berita di sini..." class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all text-slate-800 placeholder-slate-400">{{ old('content', $formDefaults['content'] ?? '') }}</textarea>
            </div>
            <div class="space-y-3 pt-4 border-t border-slate-100">
              <label class="text-sm font-semibold text-slate-700">Status Publikasi</label>
              <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="is_published" value="1" class="sr-only peer/pub" @checked((string) old('is_published', $formDefaults['is_published'] ?? '1') === '1')>
                  <div class="peer-checked/pub:bg-emerald-50 peer-checked/pub:border-emerald-500 peer-checked/pub:text-emerald-700
                            flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 cursor-pointer transition-all hover:border-emerald-300 text-slate-600" id="label-pub-create">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-300 peer-checked/pub:bg-emerald-500 transition-colors" id="dot-pub-create"></span>
                    <p class="text-sm font-semibold transition-colors" id="text-pub-create">Publik</p>
                  </div>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="is_published" value="0" class="sr-only peer/draft" @checked((string) old('is_published', $formDefaults['is_published'] ?? '1') === '0')>
                  <div class="peer-checked/draft:bg-amber-50 peer-checked/draft:border-amber-500 peer-checked/draft:text-amber-700
                            flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 cursor-pointer transition-all hover:border-amber-300 text-slate-600" id="label-draft-create">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-300 peer-checked/draft:bg-amber-500 transition-colors" id="dot-draft-create"></span>
                    <p class="text-sm font-semibold transition-colors" id="text-draft-create">Draft</p>
                  </div>
                </label>
                <p class="text-xs text-slate-500 italic">Publik = tampil di portal. Draft = hanya tersimpan di admin.</p>
              </div>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
              <a href="{{ route('admin.berita.index', request()->only(['q', 'status', 'category', 'category_id', 'page'])) }}" class="px-5 py-2.5 rounded-xl font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all text-sm focus:outline-none focus:ring-2 focus:ring-slate-400" aria-label="Kembali atau buka halaman berita">Batalkan</a>
              <button type="submit" class="px-6 py-2.5 rounded-xl font-semibold bg-gray-900 text-white hover:bg-black shadow-sm transition-all text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">Simpan Berita</button>
            </div>
          </form>
        </div>
      </section>
@endsection
