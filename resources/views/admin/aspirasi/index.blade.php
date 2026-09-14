@extends('layouts.admin')
@section('title', $pageTitle . ' - Bapperida Admin')
@section('content')
<section id="section-aspirasi" class="content-section block font-sans">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Aspirasi &amp; Pesan Warga</h1>
            <p class="text-slate-500 text-sm">Daftar masukan dan pesan publik dari halaman Hubungi Kami.</p>
          </div>
          {{-- Search & Filter Aspirasi --}}
          <div class="flex flex-wrap gap-2 items-center">
            <div class="flex gap-1 bg-slate-100 rounded-xl p-1 border border-slate-200">
              <button id="aspirasi-filter-all" onclick="filterAspirasi('all')" class="aspirasi-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition-all bg-white text-blue-600 shadow-sm">Semua</button>
              <button id="aspirasi-filter-unread" onclick="filterAspirasi('unread')" class="aspirasi-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition-all text-slate-600 hover:text-slate-900">Belum Selesai</button>
              <button id="aspirasi-filter-resolved" onclick="filterAspirasi('resolved')" class="aspirasi-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition-all text-slate-600 hover:text-slate-900">Selesai</button>
            </div>
          </div>
        </div>

        <div id="aspirasi-grid" data-admin-live="aspirasi-grid" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          @forelse($contacts as $msg)
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between transition-all hover:shadow-md">
            <div>
              <div class="flex justify-between items-start mb-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $msg->status == 'resolved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                  {{ $msg->status == 'resolved' ? 'Selesai' : 'Belum Dibaca' }}
                </span>
                <span class="text-xs text-slate-400 font-medium">{{ $msg->created_at->diffForHumans() }}</span>
              </div>
              <h3 class="font-bold text-slate-900 text-base mb-1">{{ $msg->subject }}</h3>
              <p class="text-xs font-semibold text-slate-500 mb-3">Dari: {{ $msg->name }} &bull; <span class="text-slate-400 font-normal">{{ $msg->email }}</span></p>
              <p class="text-slate-600 text-sm mb-4 leading-relaxed bg-slate-50 p-3.5 rounded-xl border border-slate-100">{{ $msg->message }}</p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-4 mt-2">
               @if($msg->status == 'unread')
               <form action="{{ \App\Support\AdminNavigation::url('admin.resolve_contact', $msg->id) }}" method="POST" class="resolve-form">
                 @csrf
                 @if($errors->any())<x-input-error :messages="$errors->all()" class="mb-4" role="alert" />@endif
                 <button type="button"
                   data-name="{{ $msg->name }}"
                   class="resolve-btn text-xs font-semibold text-blue-600 hover:text-emerald-600 flex items-center gap-1.5 transition-colors focus:outline-none focus:underline">
                   <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                   Tandai Selesai
                 </button>
               </form>
               @else
                 <div></div>
               @endif
               <form action="{{ \App\Support\AdminNavigation::url('admin.delete_contact', $msg->id) }}" method="POST" class="inline">
                 @csrf
                 @if($errors->any())<x-input-error :messages="$errors->all()" class="mb-4" role="alert" />@endif
                 @method('DELETE')
                 <button type="button" onclick="confirmDelete(this.closest('form'), 'Yakin ingin menghapus pesan dari {{ addslashes($msg->name) }}?')" class="text-xs font-semibold text-slate-400 hover:text-rose-600 flex items-center gap-1.5 transition-colors focus:outline-none">
                   <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                   Hapus
                 </button>
               </form>
            </div>
          </div>
          @empty
          <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-slate-200 text-slate-400 font-medium">
            Belum ada pesan aspirasi masuk.
          </div>
          @endforelse
        </div>
        {{-- Pagination Aspirasi --}}
        <div data-admin-live="contacts-pagination"><x-admin.pagination :paginator="$contacts" /></div>
      </section>
@endsection
