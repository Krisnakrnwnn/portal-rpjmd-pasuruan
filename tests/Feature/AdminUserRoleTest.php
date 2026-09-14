<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_and_edit_regular_users(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Super Admin']));
        $this->get(route('admin.pengguna.create'))->assertOk()->assertSee('value="User"', false);
        $this->post(route('admin.store_user'), [
            'name' => 'Pengguna Uji', 'email' => 'user@example.test',
            'password' => 'password123', 'role' => 'User',
        ])->assertSessionHasNoErrors()->assertSessionHas('success');
        $user = User::where('email', 'user@example.test')->firstOrFail();
        $this->assertSame('User', $user->role);
        $this->assertDatabaseHas('activities', ['type' => 'Pengguna', 'action' => 'Buat']);
        $this->get(route('admin.pengguna.edit', $user))->assertOk()
            ->assertSee('value="User" selected', false);

        foreach (['Admin', 'User', 'Super Admin'] as $role) {
            $this->put(route('admin.update_user', $user), [
                'name' => $user->name, 'email' => $user->email, 'role' => $role,
            ])->assertSessionHasNoErrors()->assertSessionHas('success');
            $this->assertSame($role, $user->fresh()->role);
        }
        $this->assertDatabaseHas('activities', ['type' => 'Pengguna', 'action' => 'Update']);
    }

    public function test_unknown_roles_are_rejected_on_create_and_update(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Super Admin']));
        $user = User::factory()->create(['role' => 'User']);
        $this->post(route('admin.store_user'), [
            'name' => 'Invalid Role', 'email' => 'invalid@example.test',
            'password' => 'password123', 'role' => 'Owner',
        ])->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'invalid@example.test']);
        $this->put(route('admin.update_user', $user), [
            'name' => $user->name, 'email' => $user->email, 'role' => 'Owner',
        ])->assertSessionHasErrors('role');
        $this->assertSame('User', $user->fresh()->role);
    }

    public function test_admin_cannot_manage_regular_users(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        $user = User::factory()->create(['role' => 'User']);
        $this->post(route('admin.store_user'), [
            'name' => 'Blocked User', 'email' => 'blocked@example.test',
            'password' => 'password123', 'role' => 'User',
        ])->assertRedirect(route('admin.dashboard'));
        $this->put(route('admin.update_user', $user), [
            'name' => 'Changed', 'email' => $user->email, 'role' => 'Admin',
        ])->assertRedirect(route('admin.dashboard'));
        $this->assertDatabaseMissing('users', ['email' => 'blocked@example.test']);
        $this->assertSame('User', $user->fresh()->role);
    }
}
