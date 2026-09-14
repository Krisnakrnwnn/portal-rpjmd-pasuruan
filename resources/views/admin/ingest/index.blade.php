@extends('layouts.admin')
@section('title', $pageTitle . ' - Bapperida Admin')
@section('content')
<section id="section-ingest" class="content-section block font-sans">
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Ingest Data Chatbot</h1>
            <p class="text-slate-500 text-sm">Unggah dokumen PDF RPJMD untuk melatih basis pengetahuan AI Chatbot.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sticky top-24">
              <h2 class="text-lg font-bold text-slate-900 mb-4">Unggah Dokumen</h2>
              
              <form id="ingest-form" class="space-y-5">
                @csrf
                @if($errors->any())<x-input-error :messages="$errors->all()" class="mb-4" role="alert" />@endif
                <div class="space-y-3">
                  <div class="flex items-center justify-center w-full">
                    <label for="pdf_file" class="flex flex-col items-center justify-center w-full h-44 border border-dashed border-slate-300 rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100/80 transition-all group">
                      <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                        <svg class="w-8 h-8 mb-2.5 text-slate-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p class="mb-1 text-xs text-slate-600 font-semibold"><span class="text-blue-600">Klik untuk upload</span> atau drag & drop</p>
                        <p class="text-[11px] text-slate-400">PDF (Maks. 50MB)</p>
                      </div>
                      <input id="pdf_file" name="pdf_file" type="file" class="hidden" accept=".pdf" required />
                    </label>
                  </div>
                  <div id="file-name-preview" class="hidden text-xs font-semibold text-blue-700 bg-blue-50 p-3 rounded-xl border border-blue-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="truncate"></span>
                  </div>
                </div>

                <button type="submit" id="btn-start-ingest" class="btn-ingest w-full py-3 bg-gray-900 hover:bg-black text-white font-semibold text-sm rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-gray-900 disabled:opacity-50 disabled:cursor-not-allowed">
                  <span class="spinner hidden"></span>
                  <span id="btn-text">Mulai Ingest Dokumen</span>
                </button>
              </form>
            </div>
          </div>

          <div class="lg:col-span-2">
            <div id="ingest-progress-card" class="hidden bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">
               <div class="flex items-center justify-between mb-6">
                  <div>
                    <h3 id="ingest-file-title" class="text-base font-bold text-slate-900 truncate max-w-md">Memproses Dokumen...</h3>
                    <p id="ingest-status-text" class="text-xs text-blue-600 font-medium flex items-center gap-2 mt-0.5">
                      <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                      </span>
                      Menghubungkan ke Gemini AI...
                    </p>
                  </div>
                  <div class="text-right">
                    <span id="ingest-percentage" class="text-3xl font-extrabold tracking-tight text-slate-900">0%</span>
                  </div>
               </div>

               <!-- Progress Bar -->
               <div class="w-full h-4 bg-slate-100 rounded-full overflow-hidden mb-6 p-0.5 border border-slate-200">
                  <div id="ingest-progress-bar" class="h-full bg-blue-600 rounded-full transition-all duration-700 ease-out relative" style="width: 0%">
                    <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                  </div>
               </div>

               <div class="grid grid-cols-2 gap-4">
                 <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                   <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Halaman Terproses</p>
                   <p id="ingest-pages-info" class="text-lg font-bold text-slate-900">0 / 0</p>
                 </div>
                 <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                   <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Estimasi Selesai</p>
                   <p id="ingest-eta" class="text-lg font-bold text-slate-900">Menghitung...</p>
                 </div>
               </div>

               <!-- Cancel Ingest Button -->
               <div class="mt-5 flex justify-end">
                 <button type="button" id="btn-cancel-ingest" onclick="cancelActiveIngestion()" class="px-4 py-2 bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200 hover:border-rose-200 font-semibold rounded-xl transition-all text-xs flex items-center gap-1.5 focus:outline-none">
                   <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                   Batalkan Ingest
                 </button>
               </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
               <div class="p-5 border-b border-slate-200 bg-slate-50/50">
                 <h3 class="font-bold text-slate-900 text-sm">Riwayat Ingest Dokumen</h3>
               </div>
               <div class="w-full overflow-x-auto">
                 <table class="w-full text-left border-collapse">
                   <thead>
                     <tr class="border-b border-slate-200 bg-slate-50/75">
                       <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">Nama File</th>
                       <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider text-center">Status</th>
                       <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider text-center">Tanggal</th>
                       <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Aksi</th>
                     </tr>
                   </thead>
                   <tbody id="ingest-history-body" data-admin-live="ingest-history-body" class="divide-y divide-slate-100 text-sm">
                     
                     @forelse($ingestions as $ing)
                     <tr class="hover:bg-slate-50/80 transition-colors">
                       <td class="px-6 py-4">
                         <div class="font-semibold text-slate-900 truncate max-w-xs">{{ $ing->original_name }}</div>
                         <div class="text-xs text-slate-400 font-medium">{{ $ing->processed_pages }} / {{ $ing->total_pages }} Halaman</div>
                       </td>
                       <td class="px-6 py-4 text-center">
                         @if($ing->status === 'completed')
                           <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Berhasil</span>
                         @elseif($ing->status === 'failed')
                           <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200" title="{{ $ing->error_message }}">Gagal</span>
                         @elseif($ing->status === 'cancelled')
                           <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">Batal</span>
                         @else
                           <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 animate-pulse">Proses</span>
                         @endif
                       </td>
                       <td class="px-6 py-4 text-center text-xs text-slate-500 font-medium whitespace-nowrap">
                         {{ $ing->created_at->format('d/m/Y H:i') }}
                       </td>
                       <td class="px-6 py-4 text-right whitespace-nowrap">
                         @if($ing->status !== 'processing' && $ing->status !== 'pending')
                           <button type="button" onclick="deleteIngestion({{ $ing->id }}, '{{ addslashes($ing->original_name) }}')" class="text-xs px-3 py-1 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition-all cursor-pointer font-semibold border border-rose-200 hover:border-transparent focus:outline-none">
                             Hapus
                           </button>
                         @endif
                       </td>
                     </tr>
                     @empty
                     <tr>
                       <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium">Belum ada riwayat ingest dokumen.</td>
                     </tr>
                     @endforelse
                   </tbody>
                 </table>
               </div>
               {{-- Pagination Ingest --}}
               <div data-admin-live="ingestions-pagination"><x-admin.pagination :paginator="$ingestions" /></div>
            </div>
          </div>
        </div>
      </section>
@endsection
@push('styles')
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
@endpush
@push('scripts')
@include('admin.scripts.ingest')
@endpush
