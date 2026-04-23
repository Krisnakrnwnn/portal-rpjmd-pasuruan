<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .glass {
                background: rgba(255, 255, 255, 0.75);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            .animate-ken-burns {
                animation: kenBurns 20s ease-in-out infinite alternate;
            }
            @keyframes kenBurns {
                0% { transform: scale(1); }
                100% { transform: scale(1.1); }
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased overflow-hidden">
        <div class="min-h-screen relative flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <!-- Cinematic Background -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('hero.png') }}" class="w-full h-full object-cover animate-ken-burns" alt="Background">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-900/90 via-blue-900/70 to-blue-800/80"></div>
            </div>

            <div class="relative z-10 w-full max-w-md">
                <div class="text-center mb-10">
                    <a href="/" class="inline-flex items-center gap-3 group">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center shadow-2xl group-hover:rotate-6 transition-transform">
                            <span class="text-white font-black text-2xl">R</span>
                        </div>
                        <div class="text-left">
                            <div class="font-black text-2xl tracking-tight text-white leading-none">RPJMD</div>
                            <div class="text-[10px] uppercase font-bold text-blue-200 tracking-[0.2em]">Kabupaten Pasuruan</div>
                        </div>
                    </a>
                </div>

                <div class="glass rounded-[2.5rem] p-8 md:p-10 shadow-2xl border border-white/20">
                    {{ $slot }}
                </div>
                
                <p class="text-center mt-8 text-blue-200/60 text-xs font-bold uppercase tracking-widest">
                    &copy; 2026 Pemerintah Kabupaten Pasuruan
                </p>
            </div>
        </div>
    </body>
</html>
