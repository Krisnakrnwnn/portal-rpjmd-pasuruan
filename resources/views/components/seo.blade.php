@props([
    'title' => 'Bapperida Kabupaten Pasuruan - Layanan Informasi RPJMD',
    'description' => 'Portal Resmi Badan Perencanaan Pembangunan, Riset, dan Inovasi Daerah (Bapperida) Kabupaten Pasuruan. Transparansi data perencanaan dan capaian pembangunan.',
    'image' => null,
    'type' => 'website',
    'url' => null,
    'keywords' => 'Bapperida, Kabupaten Pasuruan, RPJMD, Perencanaan Pembangunan, Riset, Inovasi Daerah, Transparansi, Capaian Pembangunan',
    'author' => 'Bapperida Kabupaten Pasuruan',
    'publishedTime' => null,
    'modifiedTime' => null
])

@php
    $currentUrl = $url ?? url()->current();
    $ogImage = $image ?? asset('hero.png');
    $fullTitle = $title . ' | Bapperida Kabupaten Pasuruan';
@endphp

<!-- Primary Meta Tags -->
<title>{{ $fullTitle }}</title>
<meta name="title" content="{{ $fullTitle }}">
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="author" content="{{ $author }}">
<meta name="robots" content="index, follow">
<meta name="language" content="Indonesian">
<meta name="revisit-after" content="7 days">

<!-- Canonical URL -->
<link rel="canonical" href="{{ $currentUrl }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $currentUrl }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:site_name" content="Bapperida Kabupaten Pasuruan">
<meta property="og:locale" content="id_ID">
@if($publishedTime)
<meta property="article:published_time" content="{{ $publishedTime }}">
@endif
@if($modifiedTime)
<meta property="article:modified_time" content="{{ $modifiedTime }}">
@endif

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $currentUrl }}">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $ogImage }}">

<!-- Additional SEO Tags -->
<meta name="geo.region" content="ID-JI">
<meta name="geo.placename" content="Kabupaten Pasuruan">
<meta name="geo.position" content="-7.6453;112.9075">
<meta name="ICBM" content="-7.6453, 112.9075">

<!-- Schema.org Structured Data -->
{{ $slot ?? '' }}

@if(empty(trim($slot ?? '')))
<script type="application/ld+json">
@php
echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'GovernmentOrganization',
    'name' => 'Badan Perencanaan Pembangunan, Riset, dan Inovasi Daerah Kabupaten Pasuruan',
    'alternateName' => 'Bapperida Kabupaten Pasuruan',
    'url' => url('/'),
    'logo' => asset('logo.png'),
    'description' => $description,
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => 'Kompleks Perkantoran Pemerintah Kabupaten Pasuruan, Gedung Berakhlak Lt. 2, Jl. Raya Raci Km. 09',
        'addressLocality' => 'Bangil',
        'addressRegion' => 'Jawa Timur',
        'postalCode' => '67153',
        'addressCountry' => 'ID'
    ],
    'contactPoint' => [
        '@type' => 'ContactPoint',
        'telephone' => '+62-343-740987',
        'contactType' => 'Customer Service',
        'email' => 'bapperida@pasuruankab.go.id',
        'areaServed' => 'ID',
        'availableLanguage' => ['Indonesian']
    ],
    'sameAs' => [
        'https://www.instagram.com/bapperida_pasuruan',
        'https://www.facebook.com/bapperida.pasuruan'
    ]
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
@endphp
</script>
@endif
