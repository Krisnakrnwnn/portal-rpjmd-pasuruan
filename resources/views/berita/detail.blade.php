@extends('layouts.app')

@section('title', $news->title . ' - RPJMD Kota Pasuruan')
@section('meta_description', $meta_description)
@section('og_image', $og_image)

@section('content')
    <!-- Header Spanduk Minimalis -->
    <div class="relative w-full min-h-[400px] bg-blue-950 overflow-hidden flex items-center">
      <div class="absolute inset-0 bg-gradient-to-r from-[#0A3D91] to-[#041a42] opacity-90"></div>
      <img src="{{ asset('hero.png') }}" class="absolute inset-0 w-full h-full object-cover opacity-10 mix-blend-overlay" />
      
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-20 pb-32">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-blue-200 text-xs uppercase tracking-widest font-bold mb-6" data-aos="fade-down">
          <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
          <span>/</span>
          <a href="{{ route('berita') }}" class="hover:text-white transition-colors">Layanan Informasi</a>
          <span>/</span>
          <span class="text-white">Detail Berita</span>
        </div>
        <h1 class="text-3xl md:text-5xl font-black text-white leading-tight max-w-4xl drop-shadow-lg" data-aos="fade-up" data-aos-delay="200">
          {{ $news->title }}
        </h1>
      </div>
    </div>

    <!-- Artikel Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-24 relative z-20">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        
        <!-- Kolom Utama Artikel -->
        <div class="lg:col-span-2" data-aos="fade-up" data-aos-delay="300">
          <article class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden text-lg">
             <!-- Hero Image Berita -->
             <div class="w-full h-[400px] overflow-hidden">
               <img src="{{ Str::startsWith($news->image_url, 'http') ? $news->image_url : asset($news->image_url ?? 'news1.png') }}" alt="{{ $news->title }}" class="w-full h-full object-cover" />
             </div>
             
             <div class="p-8 md:p-12">
                <!-- Meta Data -->
                <div class="flex flex-wrap items-center gap-6 mb-10 pb-8 border-b border-gray-100">
                   <div class="flex items-center gap-2 text-gray-500 text-sm">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path></svg>
                      <span class="font-semibold uppercase tracking-wider">{{ $news->published_at ? $news->published_at->format('d M Y') : $news->created_at->format('d M Y') }}</span>
                   </div>
                   <div class="flex items-center gap-2 text-gray-500 text-sm">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                      <span class="font-semibold uppercase tracking-wider">{{ $news->category }}</span>
                   </div>
                    <div class="flex items-center gap-2 text-gray-500 text-sm">
                       <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                       <span class="font-semibold uppercase tracking-wider">{{ $news->author->role ?? 'Admin' }}</span>
                    </div>
                </div>

                <!-- Content Body -->
                <div class="prose prose-blue max-w-none text-gray-700 leading-relaxed space-y-6">
                   {!! nl2br(e($news->content)) !!}
                </div>
                </div>

                <!-- Tombol Kembali -->
                <div class="mt-16 pt-10 border-t border-gray-100 flex justify-between items-center">
                   <a href="{{ route('berita') }}" class="flex items-center gap-2 text-blue-600 font-bold hover:gap-4 transition-all uppercase tracking-widest text-xs">
                      &larr; Kembali ke Daftar Berita
                   </a>
                   <div class="flex items-center gap-4">
                      <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Bagikan:</span>
                      <div class="flex gap-2">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center cursor-pointer hover:bg-blue-600 hover:text-white transition-all"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></div>
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center cursor-pointer hover:bg-blue-600 hover:text-white transition-all"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm3 8h-1.35c-.538 0-.65.221-.65.778v1.222h2l-.209 2h-1.791v7h-3v-7h-2v-2h2v-2.308c0-1.769.931-2.692 3.029-2.692h1.971v3z"/></svg></div>
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center cursor-pointer hover:bg-blue-600 hover:text-white transition-all"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.17.054 1.805.249 2.227.412.558.217.957.477 1.377.896.419.42.679.819.896 1.377.164.422.359 1.057.412 2.227.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.054 1.17-.248 1.805-.412 2.227-.217.558-.477.957-.896 1.377-.42.419-.819.679-1.377.896-.422.164-1.057.359-2.227.412-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.054-1.805-.249-2.227-.412-.558-.217-.957-.477-1.377-.896-.419-.42-.679-.819-.896-1.377-.164-.422-.359-1.057-.412-2.227-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.054-1.17.249-1.805.412-2.227.217-.558.477-.957.896-1.377.42-.42.819-.679 1.377-.896.422-.164 1.057-.359 2.227-.412 1.266-.058 1.646-.07 4.85-.07m0-2.163c-3.259 0-3.667.014-4.947.072-1.277.057-2.148.258-2.911.554-.789.306-1.459.717-2.126 1.384-.667.667-1.078 1.337-1.384 2.126-.297.763-.497 1.634-.554 2.911-.059 1.28-.073 1.688-.073 4.947s.014 3.667.072 4.947c.057 1.277.258 2.148.554 2.911.306.789.717 1.459 1.384 2.126.667.667 1.337 1.078 2.126 1.384.763.297 1.634.497 2.911.554 1.28.059 1.688.073 4.947.073s3.667-.014 4.947-.072c1.277-.057 2.148-.258 2.911-.554.789-.306 1.459-.717 2.126-1.384.667-.667 1.078-1.337 1.384-2.126.297-.763.497-1.634.554-2.911.059-1.28.073-1.688.073-4.947s-.014-3.667-.072-4.947c-.057-1.277-.258-2.148-.554-2.911-.306-.789-.717-1.459-1.384-2.126-.667-.667-1.337-1.078-2.126-1.384-.763-.297-1.634-.497-2.911-.554-1.28-.059-1.688-.073-4.947-.073z"/></svg></div>
                      </div>
                   </div>
                </div>
             </div>
          </article>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-12" data-aos="fade-left" data-aos-delay="400">
           <!-- Widget Berita Terkait -->
           <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
              <h4 class="font-black text-xs uppercase tracking-[0.3em] text-blue-600 mb-8 border-b border-blue-50 pb-4">Artikel Terkait</h4>
              <div class="space-y-8">
                 <a href="#" class="group flex gap-4">
                    <div class="w-16 h-16 rounded-xl bg-gray-200 overflow-hidden shrink-0">
                       <img src="{{ asset('news2.png') }}" class="w-full h-full object-cover group-hover:scale-110 transition-all" />
                    </div>
                    <div>
                       <h5 class="font-bold text-sm text-gray-900 leading-snug group-hover:text-blue-600 transition-colors">Dokumen RPJMD Ditetapkan Perda</h5>
                       <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">10 April 2026</p>
                    </div>
                 </a>
                 <a href="#" class="group flex gap-4">
                    <div class="w-16 h-16 rounded-xl bg-gray-200 overflow-hidden shrink-0">
                       <img src="{{ asset('news3.png') }}" class="w-full h-full object-cover group-hover:scale-110 transition-all" />
                    </div>
                    <div>
                       <h5 class="font-bold text-sm text-gray-900 leading-snug group-hover:text-blue-600 transition-colors">Progres Infrastruktur Capai 78%</h5>
                       <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">05 April 2026</p>
                    </div>
                 </a>
              </div>
           </div>

           <!-- Widget Kategori -->
           <div class="bg-blue-900 rounded-3xl p-8 shadow-xl text-white">
              <h4 class="font-black text-xs uppercase tracking-[0.3em] text-blue-300 mb-8 border-b border-blue-800 pb-4">Kategori</h4>
              <ul class="space-y-4 font-bold text-sm">
                 <li class="flex justify-between items-center group cursor-pointer hover:text-yellow-400 transition-all">
                    <span>Program Strategis</span>
                    <span class="w-6 h-6 rounded-full bg-blue-800 flex items-center justify-center text-[10px]">12</span>
                 </li>
                 <li class="flex justify-between items-center group cursor-pointer hover:text-yellow-400 transition-all">
                    <span>Infrastruktur</span>
                    <span class="w-6 h-6 rounded-full bg-blue-800 flex items-center justify-center text-[10px]">8</span>
                 </li>
                 <li class="flex justify-between items-center group cursor-pointer hover:text-yellow-400 transition-all">
                    <span>Musrenbang</span>
                    <span class="w-6 h-6 rounded-full bg-blue-800 flex items-center justify-center text-[10px]">4</span>
                 </li>
              </ul>
           </div>
        </div>
      </div>
    </div>
@endsection
