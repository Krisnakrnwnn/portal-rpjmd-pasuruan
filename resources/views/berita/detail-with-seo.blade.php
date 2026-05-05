@extends('layouts.app')

{{-- Contoh implementasi SEO lengkap untuk halaman detail berita --}}
{{-- File ini adalah contoh, gunakan komponen <x-seo> di file detail.blade.php yang sebenarnya --}}

@php
    $metaTitle = $news->title;
    $metaDescription = Str::limit(strip_tags($news->content), 155);
    $metaImage = Str::startsWith($news->image_url, 'http') ? $news->image_url : asset($news->image_url ?? 'news1.png');
    $metaKeywords = 'Berita Pasuruan, ' . $news->category . ', RPJMD, Bapperida, Kabupaten Pasuruan, Pembangunan Daerah';
@endphp

@section('head')
    {{-- Gunakan komponen SEO --}}
    <x-seo 
        :title="$metaTitle"
        :description="$metaDescription"
        :image="$metaImage"
        type="article"
        :keywords="$metaKeywords"
        :publishedTime="$news->published_at?->toIso8601String()"
        :modifiedTime="$news->updated_at->toIso8601String()"
    >
        <x-slot:schema>
            <script type="application/ld+json">
            {
              "@context": "https://schema.org",
              "@type": "NewsArticle",
              "headline": "{{ $news->title }}",
              "image": [
                "{{ $metaImage }}"
              ],
              "datePublished": "{{ $news->published_at?->toIso8601String() ?? $news->created_at->toIso8601String() }}",
              "dateModified": "{{ $news->updated_at->toIso8601String() }}",
              "author": {
                "@type": "Organization",
                "name": "Bapperida Kabupaten Pasuruan",
                "url": "{{ url('/') }}"
              },
              "publisher": {
                "@type": "Organization",
                "name": "Bapperida Kabupaten Pasuruan",
                "logo": {
                  "@type": "ImageObject",
                  "url": "{{ asset('logo.png') }}",
                  "width": 600,
                  "height": 60
                }
              },
              "description": "{{ $metaDescription }}",
              "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "{{ route('berita.detail', ['slug' => $news->slug]) }}"
              },
              "articleSection": "{{ $news->category }}",
              "keywords": "{{ $metaKeywords }}",
              "inLanguage": "id-ID"
            }
            </script>
            
            {{-- Breadcrumb Schema --}}
            <script type="application/ld+json">
            {
              "@context": "https://schema.org",
              "@type": "BreadcrumbList",
              "itemListElement": [
                {
                  "@type": "ListItem",
                  "position": 1,
                  "name": "Beranda",
                  "item": "{{ route('home') }}"
                },
                {
                  "@type": "ListItem",
                  "position": 2,
                  "name": "Berita",
                  "item": "{{ route('berita') }}"
                },
                {
                  "@type": "ListItem",
                  "position": 3,
                  "name": "{{ $news->title }}",
                  "item": "{{ route('berita.detail', ['slug' => $news->slug]) }}"
                }
              ]
            }
            </script>
        </x-slot:schema>
    </x-seo>
@endsection

@section('content')
    {{-- Konten halaman detail berita sama seperti di detail.blade.php --}}
    {{-- ... --}}
@endsection
