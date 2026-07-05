<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_update_institution_profile()
    {
        // Seed initial profile data
        Profile::create([
            'key' => 'sejarah',
            'title' => 'Sejarah',
            'content' => 'Old Sejarah Content'
        ]);
        Profile::create([
            'key' => 'visi',
            'title' => 'Visi',
            'content' => 'Old Visi Content'
        ]);
        Profile::create([
            'key' => 'misi',
            'title' => 'Misi',
            'content' => 'Old Misi Content'
        ]);

        $superAdmin = User::factory()->create([
            'role' => 'Super Admin'
        ]);

        $response = $this->actingAs($superAdmin)->post(route('admin.update_profile'), [
            'profiles' => [
                'sejarah' => 'New Sejarah Content',
                'visi' => 'New Visi Content',
                'misi' => 'New Misi Content',
            ]
        ]);

        $response->assertRedirect(route('admin.dashboard') . '#section-setelan');
        $response->assertSessionHas('success', 'Profil instansi berhasil diperbarui!');

        $this->assertDatabaseHas('profiles', [
            'key' => 'sejarah',
            'content' => 'New Sejarah Content'
        ]);
        $this->assertDatabaseHas('profiles', [
            'key' => 'visi',
            'content' => 'New Visi Content'
        ]);
        $this->assertDatabaseHas('profiles', [
            'key' => 'misi',
            'content' => 'New Misi Content'
        ]);
    }
}
