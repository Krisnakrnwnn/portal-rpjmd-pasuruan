@extends('layouts.app')

@section('title', 'Dashboard Capaian Modern - RPJMD Kabupaten Pasuruan')

@push('styles')
<style>
    .glass-card {
      background: rgba(255, 255, 255, 0.7);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .text-glow {
      text-shadow: 0 0 15px rgba(37, 99, 235, 0.3);
    }
    .bento-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      grid-gap: 1.5rem;
    }
    @media (max-width: 1024px) {
      .bento-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }
    @media (max-width: 640px) {
      .bento-grid {
        grid-template-columns: 1fr;
      }
    }
</style>
@endpush

@section('content')
  <!-- Background Accents -->
  <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-400/10 rounded-full blur-[120px] pointer-events-none"></div>
  <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-yellow-400/10 rounded-full blur-[120px] pointer-events-none"></div>

  <main class="flex-grow pt-24 md:pt-32 pb-24 relative z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Header Section -->
      <div class="mb-8 md:mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6 md:gap-8">
        <div class="text-center md:text-left">
           <div class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-[10px] font-black rounded-lg uppercase tracking-widest mb-4">Statistik Real-Time</div>
           <h1 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight mb-4">
             Monitor <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-blue-800">Capaian Kota</span>
           </h1>
           <p class="text-slate-500 font-medium max-w-xl text-sm md:text-base">Akses visualisasi data integratif mengenai realisasi program prioritas Kabupaten Pasuruan selama periode RPJMD 2025-2029.</p>
        </div>
        <div class="flex flex-col items-center md:items-end">
           <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Update Terakhir</div>
           <div class="text-sm md:text-lg font-black text-slate-800 bg-white px-4 py-2 shadow-sm border border-slate-100 rounded-xl flex items-center gap-3">
              <span class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse"></span>
              {{ $lastUpdate->translatedFormat('d F Y, H:i') }} WIB
           </div>
        </div>
      </div>

      <!-- Bento Grid Statistics -->
      <div class="bento-grid mb-16">
        
        <!-- Big Card: Total Progress -->
        <div data-aos="fade-up" data-aos-delay="100" class="col-span-1 md:col-span-2 row-span-1 bg-gradient-to-br from-blue-700 to-blue-900 rounded-[2rem] md:rounded-[2.5rem] p-8 md:p-10 text-white shadow-2xl shadow-blue-900/20 relative overflow-hidden group">
          <div class="absolute top-[-20%] right-[-10%] w-64 h-64 bg-white/10 rounded-full blur-[60px] group-hover:bg-white/20 transition-all duration-700"></div>
          <div class="relative z-10 h-full flex flex-col justify-between">
            <div>
              <div class="text-[10px] font-black text-blue-200 uppercase tracking-[0.2em] mb-2">Total Progress RPJMD</div>
              <div class="text-5xl md:text-7xl font-black mb-4 tracking-tighter">{{ $stats['total_progress'] ?? '0' }}%</div>
            </div>
            <div class="flex items-center gap-4">
               <div class="flex-grow h-2.5 bg-white/20 rounded-full overflow-hidden">
                 <div class="h-full bg-white rounded-full transition-all duration-1000" style="width: {{ $stats['total_progress'] ?? '0' }}%"></div>
               </div>
               <span class="font-black text-xs md:text-sm">+2.5%</span>
            </div>
          </div>
        </div>

        <!-- Square Card: Program -->
        <div data-aos="fade-up" data-aos-delay="200" class="bg-white rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-8 shadow-xl shadow-slate-200/50 border border-white flex flex-col justify-between hover:scale-105 transition-transform duration-300">
           <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
             <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
           </div>
           <div class="mt-4 md:mt-0">
             <div class="text-3xl md:text-4xl font-black text-slate-900 mb-1">{{ $stats['program_berjalan'] ?? '0' }}</div>
             <div class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest">Program Berjalan</div>
           </div>
        </div>
 
        <!-- Square Card: Indikator -->
        <div data-aos="fade-up" data-aos-delay="300" class="bg-white rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-8 shadow-xl shadow-slate-200/50 border border-white flex flex-col justify-between hover:scale-105 transition-transform duration-300">
           <div class="w-10 h-10 md:w-12 md:h-12 bg-yellow-50 text-yellow-600 rounded-2xl flex items-center justify-center">
             <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
           </div>
           <div class="mt-4 md:mt-0">
             <div class="text-3xl md:text-4xl font-black text-slate-900 mb-1">{{ $stats['target_terlampaui'] ?? '0' }}</div>
             <div class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest font-black text-yellow-600">Terlampaui Target</div>
           </div>
        </div>

      </div>

      <!-- Filter Controls -->
      <div class="flex flex-wrap items-center gap-3 mb-12">
         <span class="text-xs font-black text-slate-400 uppercase tracking-widest mr-2">Filter Tema:</span>
         <button onclick="filterSector('all')" id="btn-filter-all" class="filter-btn px-5 py-2 bg-blue-600 text-white rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-600/20">Semua</button>
         @foreach($sectors as $sec)
           <button onclick="filterSector('sector-{{ $sec->id }}')" id="btn-filter-sector-{{ $sec->id }}" class="filter-btn px-5 py-2 bg-white text-slate-600 hover:bg-slate-100 rounded-full text-[10px] font-black uppercase tracking-widest transition-colors font-bold">{{ $sec->name }}</button>
         @endforeach
      </div>

      <!-- Indicators Grid Modern (Dynamic) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-10" id="sectors-grid">
        
        @forelse($sectors as $index => $sector)
        <div class="sector-card bg-white rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-10 hover:shadow-2xl hover:shadow-{{ $sector->theme_color }}-500/10 transition-all duration-500 border border-slate-100 group" data-category="sector-{{ $sector->id }}">
           <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 md:mb-10 gap-4">
              <div class="flex items-center gap-4">
                 @php
                     $safeTheme = $sector->theme_color ?: 'blue';
                     $shade = $safeTheme == 'yellow' || $safeTheme == 'green' ? '500' : '600';
                     $defaultIcon = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>';
                 @endphp
                 <div class="w-12 h-12 md:w-14 md:h-14 bg-slate-900 text-white rounded-[1rem] md:rounded-[1.25rem] flex items-center justify-center shadow-xl group-hover:bg-{{ $safeTheme }}-{{ $shade }} transition-colors">
                    <div class="w-5 h-5 md:w-6 md:h-6">{!! $sector->icon ?: $defaultIcon !!}</div>
                 </div>
                 <div>
                    <h3 class="font-black text-lg md:text-2xl text-slate-900 leading-tight">{{ $sector->name }}</h3>
                    <p class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest">Sektor {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</p>
                 </div>
              </div>
              
              @php
                  $average = $sector->indicators->count() > 0 ? round($sector->indicators->avg('progress')) : 0;
                  $status = $average >= 80 ? 'Optimal' : ($average >= 50 ? 'Berjalan' : 'Kritis');
                  $statusColor = $average >= 80 ? 'text-green-500' : ($average >= 50 ? 'text-yellow-500' : 'text-red-500');
              @endphp
              <div class="flex items-center justify-between sm:flex-col sm:items-end bg-slate-50 sm:bg-transparent p-3 sm:p-0 rounded-xl sm:rounded-none">
                 <div class="text-2xl md:text-3xl font-black text-{{ $safeTheme }}-{{ $shade }}">{{ $average }}%</div>
                 <div class="text-[9px] font-black {{ $statusColor }} uppercase tracking-tighter">{{ $status }}</div>
              </div>
           </div>
           
           <div class="space-y-8 md:space-y-10">
              @forelse($sector->indicators as $ind)
              <div class="relative">
                 <div class="flex justify-between items-center mb-3">
                    <span class="text-[10px] md:text-xs font-black text-slate-500 uppercase tracking-wider line-clamp-1 pr-4">{{ $ind->name }}</span>
                    <span class="text-xs font-black text-slate-900">{{ $ind->progress }}%</span>
                 </div>
                 <div class="h-6 md:h-8 bg-slate-50 rounded-2xl p-1 md:p-1.5 flex items-center border border-slate-100">
                    <div class="h-full bg-{{ $safeTheme }}-{{ $shade }} rounded-xl transition-all duration-1000 shadow-lg shadow-{{ $safeTheme }}-500/40" style="width: {{ $ind->progress }}%"></div>
                 </div>
              </div>
              @empty
              <div class="text-center text-xs font-bold text-gray-400 py-4">Belum ada indikator.</div>
              @endforelse
           </div>
        </div>
        @empty
        <div class="col-span-1 md:col-span-2 text-center py-12">
            <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Belum ada data sektor capaian.</p>
        </div>
        @endforelse

      </div>

      <!-- Info Footer Dashboard -->
      <div class="mt-24 p-8 glass-card rounded-[2rem] border border-white/50 text-center">
         <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.4em] mb-4">Layanan Pengolahan Data Strategis</p>
         <p class="text-slate-500 text-sm font-medium italic">"Membangun Transparansi Pembangunan Lewat Integrasi Data Berkala Untuk Pasuruan Kota Madani."</p>
      </div>

    </div>
  </main>
@endsection

@push('scripts')
<script>
    function filterSector(category) {
        // Reset button styles
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.className = "filter-btn px-5 py-2 bg-white text-slate-600 hover:bg-slate-100 rounded-full text-[10px] font-black uppercase tracking-widest transition-colors font-bold";
        });
        
        // Set active button style
        const activeBtn = document.getElementById('btn-filter-' + category);
        if (activeBtn) {
            activeBtn.className = "filter-btn px-5 py-2 bg-blue-600 text-white rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-600/20";
        }

        // Filter cards
        document.querySelectorAll('.sector-card').forEach(card => {
            if (category === 'all' || card.dataset.category === category) {
                card.style.display = 'block';
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 50);
            } else {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => { card.style.display = 'none'; }, 300);
            }
        });
    }
</script>
@endpush
