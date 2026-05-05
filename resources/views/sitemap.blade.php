<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    
    <!-- Homepage -->
    <url>
        <loc>{{ route('home') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    <!-- Profil -->
    <url>
        <loc>{{ route('profil') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    
    <!-- Berita -->
    <url>
        <loc>{{ route('berita') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    
    <!-- Layanan -->
    <url>
        <loc>{{ route('layanan') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    
    <!-- Kontak -->
    <url>
        <loc>{{ route('kontak') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    
    <!-- News Articles -->
    @foreach($news as $article)
    <url>
        <loc>{{ route('berita.detail', ['slug' => $article->slug]) }}</loc>
        <lastmod>{{ $article->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
        @if($article->image_url)
        <image:image>
            <image:loc>{{ Str::startsWith($article->image_url, 'http') ? $article->image_url : url($article->image_url) }}</image:loc>
            <image:title>{{ $article->title }}</image:title>
        </image:image>
        @endif
    </url>
    @endforeach
    
</urlset>
