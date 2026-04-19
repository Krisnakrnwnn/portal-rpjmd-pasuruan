@extends('layouts.app')

@section('title', 'Layanan Informasi RPJMD Kota Pasuruan - Kontak')

@section('content')
    <!-- Header Spanduk -->
    <div class="relative w-full min-h-[400px] bg-blue-950 overflow-hidden mb-16 flex items-center">
      <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5"></div>
      <div class="absolute inset-0 bg-gradient-to-b from-[#0A3D91] via-blue-900/40 to-[#041a42]"></div>
      
      <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-8 pb-32 text-center flex flex-col items-center">
        <!-- Breadcrumbs UI -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 mb-6 text-sm text-blue-200">
          <a href="{{ route('home') }}" class="hover:text-white cursor-pointer transition-colors text-xs uppercase tracking-wider font-semibold">Beranda</a>
          <span>/</span>
          <span class="text-white text-xs uppercase tracking-wider font-bold">Kontak Layanan</span>
        </div>
        
        <h1 class="text-4xl md:text-6xl font-black text-white mb-6 drop-shadow-2xl">
          Sampaikan <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500">Aspirasi Anda</span>
        </h1>
        <p class="text-blue-100/90 text-lg md:text-xl max-w-2xl font-light leading-relaxed">
          Kami hadir untuk masyarakat Kota Pasuruan. Pertanyaan, masukan, maupun saran terkait RPJMD Kota Pasuruan sangat kami harapkan demi perencanaan yang lebih baik.
        </p>
      </div>

      <!-- Bottom Curve -->
      <div class="absolute bottom-0 w-full overflow-hidden leading-none z-20 translate-y-[2px]">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-[50px] lg:h-[80px]">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C54,82.47,110.15,77.58,168.22,76.54,219.64,75.64,271.86,65.68,321.39,56.44Z" fill="#F9FAFB"></path>
        </svg>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 w-full flex flex-col lg:flex-row gap-12 -mt-10 relative z-30">
      <!-- Informasi Kontak -->
      <div class="lg:w-1/3 space-y-6">
        <div class="flex items-center p-6 bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow" data-aos="fade-up" data-aos-delay="100">
          <div class="w-14 h-14 bg-blue-50 border border-blue-100 rounded-full flex items-center justify-center text-blue-600 shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
          </div>
          <div class="ml-5">
            <h4 class="font-extrabold text-gray-900 text-lg">Alamat Kantor</h4>
            <p class="text-gray-500 text-sm mt-1">Jl. Pahlawan No. 44<br>Kota Pasuruan, Jawa Timur 67114</p>
          </div>
        </div>
        
        <div class="flex items-center p-6 bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow" data-aos="fade-up" data-aos-delay="200">
          <div class="w-14 h-14 bg-yellow-50 border border-yellow-100 rounded-full flex items-center justify-center text-yellow-600 shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
          </div>
          <div class="ml-5">
            <h4 class="font-extrabold text-gray-900 text-lg">Telepon</h4>
            <p class="text-gray-500 text-sm mt-1">(0343) 421 230<br>Senin - Jumat, 08:00 - 16:00</p>
          </div>
        </div>
        
        <div class="flex items-center p-6 bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow" data-aos="fade-up" data-aos-delay="300">
          <div class="w-14 h-14 bg-green-50 border border-green-100 rounded-full flex items-center justify-center text-green-600 shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
          </div>
          <div class="ml-5">
            <h4 class="font-extrabold text-gray-900 text-lg">Online Service</h4>
            <p class="text-gray-500 text-sm mt-1">rpjmd@pasuruankota.go.id<br>Terintegrasi portal layanan.</p>
          </div>
        </div>
      </div>
      
      <!-- Formulir Kontak -->
      <div class="lg:w-2/3 bg-white rounded-3xl shadow-xl border border-gray-100 p-8 md:p-12 scale-100 transform transition-all hover:scale-[1.01]" data-aos="fade-left">
        <h3 class="text-3xl font-extrabold mb-8 text-blue-900 border-b border-gray-100 pb-4">Tinggalkan Pesan Disini</h3>
        
        @if(session('success'))
        <div class="mb-8 p-6 bg-green-50 border-l-4 border-green-500 rounded-xl flex items-center gap-4 animate-bounce">
            <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
                <p class="text-green-800 font-bold">Terima Kasih!</p>
                <p class="text-green-600 text-sm font-medium">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <form action="{{ route('kontak.store') }}" method="POST" class="space-y-6">
          @csrf
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Nama Pengirim</label>
              <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 focus:bg-white transition-all text-sm font-medium" placeholder="Masukkan nama..." />
              @error('name') <p class="text-red-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
            </div>
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Email Lengkap</label>
              <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 focus:bg-white transition-all text-sm font-medium" placeholder="anda@email.com" />
              @error('email') <p class="text-red-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
            </div>
          </div>
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Topik Subjek Pertanyaan</label>
            <input type="text" name="subject" value="{{ old('subject') }}" required class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 focus:bg-white transition-all text-sm font-medium" placeholder="Mohon bantuan jadwal Musrenbang" />
            @error('subject') <p class="text-red-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Pesan Inti</label>
            <textarea name="message" required rows="5" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 focus:bg-white transition-all text-sm font-medium" placeholder="Tuliskan aspirasi dan kendala teknis Anda di sini secara runut...">{{ old('message') }}</textarea>
            @error('message') <p class="text-red-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
          </div>
          <button type="submit" class="w-full py-5 bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-extrabold text-lg rounded-xl shadow-lg hover:shadow-2xl transition-all hover:-translate-y-1 focus:outline-none">
            Terbangkan Pesan Sekarang &rarr;
          </button>
        </form>
      </div>
    </div>
@endsection
