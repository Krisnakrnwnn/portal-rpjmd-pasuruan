<!-- DNS Prefetch & Preconnect untuk performa lebih cepat -->
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link rel="dns-prefetch" href="//unpkg.com">
<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<!-- Preload critical assets -->
<link rel="preload" href="{{ asset('logo.png') }}" as="image">
<link rel="preload" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" as="style">

<!-- Favicon & App Icons -->
<link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
<link rel="shortcut icon" type="image/png" href="{{ asset('logo.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo.png') }}">
<link rel="apple-touch-icon" sizes="152x152" href="{{ asset('logo.png') }}">
<link rel="apple-touch-icon" sizes="120x120" href="{{ asset('logo.png') }}">
<link rel="apple-touch-icon" sizes="76x76" href="{{ asset('logo.png') }}">

<!-- PWA Meta Tags -->
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#2563eb">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Bapperida Pasuruan">
<meta name="mobile-web-app-capable" content="yes">

<!-- Microsoft Tiles -->
<meta name="msapplication-TileColor" content="#2563eb">
<meta name="msapplication-TileImage" content="{{ asset('logo.png') }}">
<meta name="msapplication-config" content="{{ asset('browserconfig.xml') }}">
