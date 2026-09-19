<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/png" href="{{ asset('logo.png') }}" />
  <link rel="shortcut icon" type="image/png" href="{{ asset('logo.png') }}" />
  <link rel="apple-touch-icon" href="{{ asset('logo.png') }}" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Admin Panel - Bapperida Kabupaten Pasuruan')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin.js'])
  
  <style>
    .admin-shell :is(a,button,input,select,textarea):focus-visible { outline: 2px solid #2563eb; outline-offset: 3px; }
    .admin-shell main :is(input,select,textarea) { max-width: 100%; }
    .admin-shell main .grid > *, .admin-shell main .flex > * { min-width: 0; }
    .admin-shell main p { overflow-wrap: anywhere; }
    .admin-shell [role="dialog"] { overflow-y: auto; max-height: 100svh; }
    @media (max-width: 639px) { .admin-shell main .p-8 { padding: 1.25rem; } .admin-shell main h1 { overflow-wrap: anywhere; } }
    @media (prefers-reduced-motion: reduce) { .admin-shell *, .admin-shell *::before, .admin-shell *::after { animation: none !important; transition: none !important; } }
  </style>
  @stack('styles')
</head>
<body data-admin-module="{{ $adminModule }}" class="admin-shell font-sans text-gray-800 bg-gray-50 min-h-screen">

  @include('admin.partials.sidebar')

  <div class="lg:ml-64 min-w-0 flex flex-col min-h-screen">
    
    @include('admin.partials.header')

    <main id="admin-content" class="flex-1 p-4 lg:p-8 relative overflow-x-hidden print:p-0">
      @yield('content')
    </main>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: @js(session('success')),
            showConfirmButton: false,
            timer: 2000,
            background: '#ffffff',
            color: '#1e293b',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Ups!',
            text: @js(session('error')),
            confirmButtonColor: '#2563eb',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black'
            }
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'warning',
            title: 'Validasi Gagal',
            text: @js(implode('\n', $errors->all())),
            confirmButtonColor: '#d97706',
            confirmButtonText: 'Perbaiki',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black text-yellow-600'
            }
        });
    @endif

    window.confirmDelete = function(form, message = 'Yakin ingin menghapus data ini?') {
        Swal.fire({
            title: 'Konfirmasi',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black text-gray-800'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.adminSubmit(form);
            }
        });
    }
  </script>

  @php($adminNavigation = ['home' => route('admin.dashboard'), 'legacy' => collect(['section-dashboard' => 'admin.dashboard','section-dokumen' => 'admin.dokumen.index','section-dokumen-form' => 'admin.dokumen.create','section-dokumen-edit' => 'admin.dokumen.index','section-ingest' => 'admin.ingest.index','section-pengguna' => 'admin.pengguna.index','section-pengguna-form' => 'admin.pengguna.create','section-pengguna-edit' => 'admin.pengguna.index','section-setelan' => 'admin.setelan.index'])->map(fn ($route) => route($route))])
  <script id="admin-navigation" type="application/json">@json($adminNavigation)</script>
  @stack('scripts')
</body>
</html>
