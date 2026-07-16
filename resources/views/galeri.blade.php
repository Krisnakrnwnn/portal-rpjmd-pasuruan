@extends('layouts.app')

@section('seo')
<title>Galeri | Portal Bapperida Kab. Pasuruan</title>
<meta name="description" content="Galeri kegiatan dan dokumentasi Layanan Informasi Badan Perencanaan Pembangunan, Riset, dan Inovasi Daerah Kabupaten Pasuruan." />
<meta property="og:title" content="Galeri | Portal Bapperida Kab. Pasuruan" />
<meta property="og:image" content="{{ asset('hero.png') }}" />
@endsection

@section('content')
<!-- Hero Section -->
<div class="relative w-full min-h-[450px] bg-blue-950 overflow-hidden mb-16 flex items-center">
    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500/30 rounded-full blur-[100px] animate-pulse"></div>
    <img src="{{ asset('hero.png') }}" class="absolute inset-0 w-full h-full object-cover opacity-30 mix-blend-overlay object-bottom" />
    <div class="absolute inset-0 bg-gradient-to-b from-[#041a42]/80 via-blue-900/60 to-[#0A3D91]"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-28 pb-24 text-center flex flex-col items-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 mb-6 text-sm text-blue-200">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors text-xs uppercase tracking-wider font-semibold">Beranda</a>
            <span>/</span>
            <span class="text-white text-xs uppercase tracking-wider font-bold">Galeri</span>
        </div>
        <h1 data-aos="fade-up" class="text-4xl md:text-6xl font-black text-white mb-6 drop-shadow-2xl">
            Galeri <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500">Kegiatan</span>
        </h1>
        <p data-aos="fade-up" data-aos-delay="100" class="text-blue-100 text-lg md:text-xl max-w-3xl font-light leading-relaxed">
            Jelajahi momen-momen penting dan dokumentasi visual pembangunan serta pelayanan Bapperida Kabupaten Pasuruan.
        </p>
    </div>

    <!-- Bottom Curve Divider -->
    <div class="absolute bottom-0 w-full overflow-hidden leading-none z-20 translate-y-[2px]">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-[50px] lg:h-[80px]">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C54,82.47,110.15,77.58,168.22,76.54,219.64,75.64,271.86,65.68,321.39,56.44Z" fill="#F9FAFB"></path>
        </svg>
    </div>
</div>

<!-- Gallery Grid -->
<section class="py-16 bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @if(isset($galleries) && $galleries->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($galleries as $index => $gallery)
            <div data-aos="zoom-in" data-aos-delay="{{ ($index % 8) * 50 }}" class="group flex flex-col rounded-2xl overflow-hidden bg-white shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer">
                <!-- Image Wrapper -->
                <div class="relative w-full aspect-[4/3] overflow-hidden">
                    <div class="absolute inset-0 bg-gray-100 animate-pulse"></div> <!-- Placeholder shimmer -->
                    <img src="{{ asset('images/gallery/' . $gallery->image_path) }}" 
                         alt="{{ $gallery->title }}" 
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                         onload="this.previousElementSibling.style.display='none'" />
                </div>
                
                <!-- Content / Info -->
                <div class="p-4 flex-1 flex flex-col justify-center">
                    <p class="text-gray-900 font-bold text-sm leading-tight truncate" title="{{ $gallery->title }}">{{ $gallery->title }}</p>
                    @if($gallery->location)
                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                        <i class="fas fa-map-marker-alt text-blue-500"></i> {{ $gallery->location }}
                    </p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-12 flex justify-center">
            {{ $galleries->links('pagination::tailwind') }}
        </div>
        @else
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-3xl border border-gray-100 p-8" data-aos="fade-up">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-images text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum ada Galeri</h3>
            <p class="text-gray-500 max-w-md mx-auto">Kami sedang mempersiapkan dokumentasi terbaik untuk ditampilkan di halaman ini. Silakan kunjungi kembali nanti.</p>
        </div>
        @endif
        
    </div>
</section>

<!-- Lightbox Modal (Simple) -->
<div id="lightbox" class="fixed inset-0 z-[100] bg-black/95 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4">
    <!-- Close Button -->
    <button onclick="closeLightbox()" class="absolute top-4 right-4 sm:top-8 sm:right-8 w-12 h-12 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition-colors border border-white/20 z-[101]">
        <i class="fas fa-times text-xl"></i>
    </button>
    
    <!-- Image Container -->
    <div class="relative max-w-5xl w-full max-h-[85vh] flex items-center justify-center">
        <img id="lightbox-img" src="" alt="Zoomed Image" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl scale-95 transition-transform duration-300" />
    </div>
</div>

@push('scripts')
<script>
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const images = document.querySelectorAll('.group img');
    let isOpen = false;

    // Open lightbox
    images.forEach(img => {
        img.parentElement.addEventListener('click', () => {
            lightboxImg.src = img.src;
            lightbox.classList.remove('hidden');
            // Small delay to allow display block to apply before changing opacity
            setTimeout(() => {
                lightbox.classList.remove('opacity-0');
                lightboxImg.classList.remove('scale-95');
                lightboxImg.classList.add('scale-100');
            }, 10);
            document.body.style.overflow = 'hidden'; // Prevent scrolling
            isOpen = true;
        });
    });

    // Close lightbox
    function closeLightbox() {
        lightbox.classList.add('opacity-0');
        lightboxImg.classList.remove('scale-100');
        lightboxImg.classList.add('scale-95');
        setTimeout(() => {
            lightbox.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300); // Wait for transition
        isOpen = false;
    }

    // Close on click outside
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });

    // Close on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isOpen) {
            closeLightbox();
        }
    });
</script>
@endpush
@endsection
