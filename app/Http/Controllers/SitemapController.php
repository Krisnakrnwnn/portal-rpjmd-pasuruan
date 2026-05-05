<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $news = News::where('is_published', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        $content = view('sitemap', compact('news'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }
}
