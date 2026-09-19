<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LaunchingPageTest extends TestCase
{
    public function test_launching_page_is_public_and_uses_the_launching_route(): void
    {
        $response = $this->get('/launching');

        $response->assertOk();
        $response->assertViewIs('launching.index');
        $response->assertSee('Launching');
        $response->assertSee('PRivIA');
        $response->assertSee('launching-chat-bubble', false);
        $response->assertSee('Masuk ke Portal');
        $response->assertSee('KOLABORASI');
        $response->assertSeeInOrder(['Politeknik', 'Negeri Bali']);
        $response->assertSee('Pemerintah Kabupaten Pasuruan');
        $response->assertSee('Logo Bapperida Kab Pasuruan Putih.png', false);
        $response->assertSee('launching-collaboration-mark', false);
        $response->assertDontSee('Kolaborator');
        $response->assertDontSee('Pemerintah Kabupaten</span>', false);
        $response->assertSee(route('home'), false);
    }

    public function test_public_page_route_set_keeps_only_portal_pages_and_launching(): void
    {
        foreach (['home', 'profil', 'dokumen', 'launching.index'] as $routeName) {
            $this->assertTrue(Route::has($routeName));
        }

        foreach (['berita', 'berita.detail', 'galeri', 'kontak', 'kontak.store', 'sitemap'] as $routeName) {
            $this->assertFalse(Route::has($routeName));
        }
    }
}
