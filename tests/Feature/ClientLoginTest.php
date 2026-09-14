<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/client/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke Akun Anda');
    }

    public function test_client_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'role' => 'User',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/client/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('home'));
    }

    public function test_client_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'role' => 'User',
            'password' => bcrypt('password123'),
        ]);

        $this->post('/client/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_admin_user_cannot_login_via_client_login(): void
    {
        $admin = User::factory()->create([
            'role' => 'Admin',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/client/login', [
            'email' => $admin->email,
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_client_can_logout(): void
    {
        $user = User::factory()->create([
            'role' => 'User',
        ]);

        $response = $this->actingAs($user)->post('/client/logout');

        $this->assertGuest();
        $response->assertRedirect(route('client.login'));
    }
}
