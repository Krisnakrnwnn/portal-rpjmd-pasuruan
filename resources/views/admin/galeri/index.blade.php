@extends('layouts.admin')
@section('title', $pageTitle . ' - Bapperida Admin')
@section('content')
<section id="section-galeri" class="content-section block">
  <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
      <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 tracking-tight">Manajemen Galeri</h1>
      <p class="text-sm text-slate-500 mt-1">Kelola dokumentasi gambar kegiatan dan foto publik RPJMD Pasuruan.</p>
    </div>
    <button onclick="openGalleryModal('modal-tambah-galeri')" class="px-5 py-2.5 bg-gray-900 hover:bg-black text-white font-semibold rounded-xl shadow-sm transition-all flex items-center gap-2 text-sm whitespace-nowrap focus-visible:outline-2 focus-visible:outline-gray-900">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
      Tambah Galeri Baru
    </button>
  </div>

  <div data-admin-live="galleries" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($galleries as $gallery)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col group hover:shadow-md transition-shadow">
      <div class="relative w-full aspect-[4/3] bg-slate-100">
        <img src="{{ asset('images/gallery/' . $gallery->image_path) }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 transition-opacity flex items-center justify-center gap-3">
          <button onclick="editGallery(this)" data-id="{{ $gallery->id }}" data-title="{{ $gallery->title }}" data-location="{{ $gallery->location }}" data-action="{{ \App\Support\AdminNavigation::url('admin.update_gallery', $gallery->id) }}" class="w-10 h-10 bg-white/20 hover:bg-white/40 backdrop-blur rounded-full flex items-center justify-center text-white transition-colors" title="Edit">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
          </button>
          <form action="{{ \App\Support\AdminNavigation::url('admin.delete_gallery', $gallery->id) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete(this, 'Yakin ingin menghapus galeri ini?')">
              @csrf
              @if($errors->any())<x-input-error :messages="$errors->all()" class="mb-4" role="alert" />@endif
              @method('DELETE')
              <button type="submit" class="w-10 h-10 bg-red-600/80 hover:bg-red-600 backdrop-blur rounded-full flex items-center justify-center text-white transition-colors" title="Hapus">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
          </form>
        </div>
      </div>
      <div class="p-4 flex-1">
        <h3 class="font-semibold text-slate-900 text-sm mb-1 line-clamp-2" title="{{ $gallery->title }}">{{ $gallery->title }}</h3>
        @if($gallery->location)
        <p class="text-xs text-slate-400 flex items-center gap-1 mt-1 font-medium"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>{{ $gallery->location }}</p>
        @endif
      </div>
    </div>
    @empty
    <div class="col-span-full py-16 text-center border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50">
      <div class="w-16 h-16 bg-white shadow-sm rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
      </div>
      <h3 class="text-lg font-semibold text-slate-900 mb-1">Belum ada Galeri</h3>
      <p class="text-sm text-slate-500">Tambahkan gambar galeri pertama Anda.</p>
    </div>
    @endforelse
  </div>
  <div data-admin-live="galleries-pagination" class="mt-6"><x-admin.pagination :paginator="$galleries" /></div>
</section>

<!-- Modal Tambah Galeri -->
<div id="modal-tambah-galeri" role="dialog" aria-modal="true" aria-label="Form galeri" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeGalleryModal(this.parentElement.id)"></div>
  <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
    <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full border border-slate-100">
      <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="text-lg font-semibold text-slate-900">Tambah Galeri</h3>
        <button onclick="closeGalleryModal('modal-tambah-galeri')" class="text-slate-400 hover:text-slate-700 bg-white hover:bg-slate-100 p-2 rounded-xl transition-colors shadow-sm border border-slate-200">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>
      <form action="{{ \App\Support\AdminNavigation::url('admin.store_gallery') }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        @if($errors->any())<x-input-error :messages="$errors->all()" class="mb-4" role="alert" />@endif
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Judul Kegiatan / Gambar <span class="text-red-500">*</span></label>
            <input type="text" name="title" required class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 block p-3 font-medium outline-none" value="{{ old('title', $formDefaults['title'] ?? '') }}">
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Lokasi <span class="text-xs text-slate-400 font-normal">(opsional)</span></label>
            <input type="text" name="location" placeholder="Contoh: Kab. Pasuruan" class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 block p-3 font-medium outline-none" value="{{ old('location', $formDefaults['location'] ?? '') }}">
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Gambar <span class="text-red-500">*</span></label>
            <input type="file" name="image" required accept="image/*" class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 block p-2">
            <p class="text-xs text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maks: 5MB.</p>
          </div>
        </div>
        <div class="mt-8 flex justify-end gap-3">
          <button type="button" onclick="closeGalleryModal('modal-tambah-galeri')" class="px-5 py-2.5 text-sm font-semibold text-slate-700 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
          <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-gray-900 rounded-xl hover:bg-black transition-colors shadow-sm">Simpan Galeri</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Galeri -->
<div id="modal-edit-galeri" role="dialog" aria-modal="true" aria-label="Form galeri" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeGalleryModal(this.parentElement.id)"></div>
  <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
    <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full border border-slate-100">
      <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="text-lg font-semibold text-slate-900">Edit Galeri</h3>
        <button onclick="closeGalleryModal('modal-edit-galeri')" class="text-slate-400 hover:text-slate-700 bg-white hover:bg-slate-100 p-2 rounded-xl transition-colors shadow-sm border border-slate-200">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>
      <form id="form-edit-galeri" action="{{ $record ? route('admin.update_gallery', $record->id) : '' }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        @if($errors->any())<x-input-error :messages="$errors->all()" class="mb-4" role="alert" />@endif
        @method('PUT')
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Judul Kegiatan / Gambar <span class="text-red-500">*</span></label>
            <input type="text" name="title" id="edit-galeri-title" required class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 block p-3 font-medium outline-none" value="{{ old('title', $formDefaults['title'] ?? '') }}">
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Lokasi <span class="text-xs text-slate-400 font-normal">(opsional)</span></label>
            <input type="text" name="location" id="edit-galeri-location" class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 block p-3 font-medium outline-none" value="{{ old('location', $formDefaults['location'] ?? '') }}">
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Ganti Gambar <span class="text-xs text-slate-400 font-normal">(Biarkan kosong jika tidak diganti)</span></label>
            <input type="file" name="image" accept="image/*" class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 block p-2">
          </div>
        </div>
        <div class="mt-8 flex justify-end gap-3">
          <button type="button" onclick="closeGalleryModal('modal-edit-galeri')" class="px-5 py-2.5 text-sm font-semibold text-slate-700 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
          <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-gray-900 rounded-xl hover:bg-black transition-colors shadow-sm">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  let galleryTrigger;
  function openGalleryModal(id) {
      galleryTrigger = document.activeElement;
      const modal = document.getElementById(id);
      modal.classList.remove('hidden');
      modal.querySelector('input:not([type="hidden"])')?.focus();
  }
  function closeGalleryModal(id) {
      document.getElementById(id).classList.add('hidden');
      const url = new URL(location.href); url.searchParams.delete('edit'); history.replaceState({}, '', url);
      galleryTrigger?.focus();
  }
  function editGallery(button) {
      document.getElementById('edit-galeri-title').value = button.dataset.title;
      document.getElementById('edit-galeri-location').value = button.dataset.location;
      const form = document.getElementById('form-edit-galeri');
      form.action = button.dataset.action;
      const url = new URL(location.href); url.searchParams.set('edit', button.dataset.id); history.replaceState({}, '', url);
      window.adminResetFormBaseline?.(form);
      openGalleryModal('modal-edit-galeri');
  }
  document.addEventListener('keydown', event => {
      const modal = document.querySelector('[role="dialog"]:not(.hidden)');
      if (!modal) return;
      if (event.key === 'Escape') closeGalleryModal(modal.id);
      if (event.key === 'Tab') {
          const fields = [...modal.querySelectorAll('button,input:not([type="hidden"]),select,textarea,a')].filter(el => !el.disabled);
          const first = fields[0], last = fields[fields.length-1];
          if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
          if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
      }
  });
  @if($errors->any())
      document.addEventListener('DOMContentLoaded', () => openGalleryModal(@js($record ? 'modal-edit-galeri' : 'modal-tambah-galeri')));
  @endif
</script>
@endsection
