<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200)
            ->assertDontSee('Kembali ke Portal RPJMD')
            ->assertSeeInOrder(['logo_pasuruan.png', 'Logo Bapperida Kab Pasuruan Putih.png'], false)
            ->assertSee('Buat Akun Portal')
            ->assertSee('Portal Informasi Perencanaan Daerah, Riset Dan Inovasi')
            ->assertSee('aria-labelledby="register-title"', false)
            ->assertSee('Nama Lengkap')
            ->assertSee('Konfirmasi Kata Sandi')
            ->assertSee('href="'.route('login').'"', false);
    }

    public function test_login_links_to_registration(): void
    {
        $this->get(route('login'))->assertOk()
            ->assertSee('href="'.route('register').'"', false)
            ->assertSee('Daftar sekarang')
            ->assertSee('Portal Informasi Perencanaan Daerah, Riset Dan Inovasi')
            ->assertDontSee('Kembali ke Portal RPJMD')
            ->assertSeeInOrder(['logo_pasuruan.png', 'Logo Bapperida Kab Pasuruan Putih.png'], false);
    }

    public function test_registration_displays_validation_errors(): void
    {
        $this->from('/register')->post('/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ])->assertRedirect('/register')->assertSessionHasErrors(['email', 'password']);

        $this->view('auth.register', ['errors' => session('errors')])
            ->assertSee('value="Test User"', false)
            ->assertSee('aria-describedby="email-error"', false)
            ->assertSee('aria-describedby="password-error"', false)
            ->assertDontSee('value="password"', false);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
        $this->assertSame('User', auth()->user()->role);
    }
}
