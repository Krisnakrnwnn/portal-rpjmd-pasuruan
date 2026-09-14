@extends('layouts.admin')
@section('title', $pageTitle . ' - Bapperida Admin')
@section('content')
<section id="section-dashboard" class="content-section block">
  <div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 tracking-tight">Dashboard Utama</h1>
    <p class="text-sm text-slate-500 mt-1">Ringkasan aktivitas dan operasional sistem Portal RPJMD Pasuruan.</p>
  </div>

  {{-- Summary Stats Cards --}}
  <div data-admin-live="summary" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 print:hidden">
    {{-- Bank Dokumen --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <div>
          <p class="text-3xl font-semibold text-slate-900 leading-tight">{{ $counts['documents'] }}</p>
          <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">Bank Dokumen</p>
          <p class="text-xs text-slate-400 mt-0.5">Dokumen Publik RPJMD</p>
        </div>
      </div>
    </div>

    {{-- Ingest AI --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
        </div>
        <div>
          <p class="text-3xl font-semibold text-slate-900 leading-tight">{{ $counts['ingest_chunks'] }}</p>
          <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">Chunk Ingest AI</p>
          <p class="text-xs text-slate-400 mt-0.5">Pengetahuan Chatbot</p>
        </div>
      </div>
    </div>

    {{-- Total Admin --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <div>
          <p class="text-3xl font-semibold text-slate-900 leading-tight">{{ $counts['users'] }}</p>
          <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">Total Admin</p>
          <p class="text-xs text-slate-400 mt-0.5">Staf Pengelola Sistem</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Activity Log --}}
  <div class="bg-white rounded-2xl border border-slate-200 p-6 lg:p-8 shadow-sm">
    <div class="flex flex-wrap gap-2 items-center justify-between mb-6 pb-4 border-b border-slate-100">
      <div>
        <h2 class="text-xl font-semibold text-slate-900">Log Aktivitas</h2>
        <p class="text-xs text-slate-500 mt-0.5">Catatan riwayat tindakan administratif sistem.</p>
      </div>
      {{-- Date Filter --}}
      <div class="relative flex items-center">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
        </div>
        <input type="date" id="activity-date-filter" data-admin-filter="date" value="{{ request('date') }}" aria-label="Filter tanggal aktivitas" class="text-xs font-medium border border-slate-300 rounded-xl pl-9 pr-3 py-2 outline-none focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 text-slate-700 transition-all cursor-pointer bg-slate-50 hover:bg-white hover:border-slate-400 shadow-xs">
      </div>
    </div>
    
    <div id="activity-list" data-admin-live="activities" class="space-y-3 overflow-y-auto pr-1" style="max-height: 480px;">
      @forelse($activities as $act)
      <div class="activity-item flex gap-3.5 p-3.5 rounded-xl hover:bg-slate-50 transition-colors border border-slate-100" data-date="{{ $act->created_at->setTimezone('Asia/Jakarta')->format('Y-m-d') }}">
        <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center font-semibold text-xs
          {{ $act->action == 'Hapus' ? 'bg-red-50 text-red-700' : ($act->action == 'Buat' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700') }}">
          {{ strtoupper(substr($act->type, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex justify-between items-start gap-1">
            <span class="text-xs font-semibold uppercase tracking-wide {{ $act->action == 'Hapus' ? 'text-red-700' : ($act->action == 'Buat' ? 'text-emerald-700' : 'text-blue-700') }}">
              {{ $act->action }} {{ $act->type }}
            </span>
            <span class="text-xs text-slate-400 font-medium flex-shrink-0 whitespace-nowrap">{{ $act->created_at->diffForHumans() }}</span>
          </div>
          <p class="text-sm text-slate-700 truncate mt-0.5 font-medium">{{ $act->description }}</p>
          <p class="text-xs text-slate-400 mt-0.5">— {{ $act->user->name ?? 'Sistem' }}</p>
        </div>
      </div>
      @empty
      {{-- Elemen empty state --}}
      <div id="activity-empty-state" class="text-center py-12 text-slate-400 text-sm">
          Tidak ada aktivitas pada tanggal ini.
      </div>
      @endforelse
    </div>
  </div>
  <div data-admin-live="activities-pagination" class="mt-4"><x-admin.pagination :paginator="$activities" /></div>
</section>
@endsection
