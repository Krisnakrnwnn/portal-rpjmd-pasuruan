@props(['paginator'])
@if($paginator->hasPages())
<nav aria-label="Pagination" class="admin-pagination flex flex-wrap gap-3 items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50/50">
    <span class="text-xs text-gray-400 font-medium">Menampilkan <span class="font-bold text-gray-700">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</span> dari <span class="font-bold text-gray-700">{{ $paginator->total() }}</span> data</span>
    <div class="flex flex-wrap items-center gap-1">
        @if($paginator->onFirstPage())
            <span aria-disabled="true" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-gray-50 border border-gray-100 text-gray-300">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" aria-label="Halaman sebelumnya" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-white border border-gray-200 text-gray-600 hover:bg-slate-100">‹</a>
        @endif
        @php
            $start = max(1, $paginator->currentPage() - 2);
            $end = min($paginator->lastPage(), $start + 4);
            $start = max(1, $end - 4);
        @endphp
        @for($page = $start; $page <= $end; $page++)
            <a href="{{ $paginator->url($page) }}" aria-label="Halaman {{ $page }}" @if($page === $paginator->currentPage()) aria-current="page" @endif
               class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold transition-all {{ $page === $paginator->currentPage() ? 'bg-gray-900 text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:bg-slate-100 hover:text-slate-900' }}">{{ $page }}</a>
        @endfor
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" aria-label="Halaman berikutnya" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-white border border-gray-200 text-gray-600 hover:bg-slate-100">›</a>
        @else
            <span aria-disabled="true" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-gray-50 border border-gray-100 text-gray-300">›</span>
        @endif
    </div>
</nav>
@endif
