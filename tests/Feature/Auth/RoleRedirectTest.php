<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RoleRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_login_always_goes_home_without_admin_otp(): void
    {
        Mail::fake();
        config(['admin-auth.otp.enabled' => true]);
        $user = User::factory()->create(['role' => 'User']);

        $this->withSession(['url.intended' => route('admin.dashboard')])
            ->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('home'))
            ->assertSessionMissing('url.intended');

        $this->assertAuthenticatedAs($user);
        Mail::assertNothingSent();
        $this->assertDatabaseCount('admin_login_otps', 0);
        $this->get('/admin')->assertRedirect('/');
    }

    public function test_client_login_ignores_intended_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'User']);
        $this->withSession(['url.intended' => route('dashboard')])
            ->post('/client/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('home'))
            ->assertSessionMissing('url.intended');
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_cannot_grant_admin_role_or_redirect_to_dashboard(): void
    {
        $this->withSession(['url.intended' => route('admin.dashboard')])->post('/register', [
            'name' => 'Test User',
            'email' => 'regular@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'Super Admin',
        ])->assertRedirect(route('home'))->assertSessionMissing('url.intended');

        $this->assertSame('User', auth()->user()->role);
    }

    public function test_authenticated_pages_redirect_according_to_role(): void
    {
        foreach (['User' => 'home', 'Admin' => 'admin.dashboard', 'Super Admin' => 'admin.dashboard'] as $role => $destination) {
            $this->actingAs(User::factory()->create(['role' => $role]));
            foreach (['/login', '/register', '/client/login', '/dashboard'] as $path) {
                $this->get($path)->assertRedirect(route($destination));
            }
        }
    }

    public function test_regular_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create(['role' => 'User']);
        $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
