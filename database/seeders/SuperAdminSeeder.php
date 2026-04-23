<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'bapperida@pasuruankab.go.id'],
            [
                'name' => 'Admin Bapperida',
                'password' => Hash::make('Pasuruan2026!'),
                'role' => 'Super Admin',
            ]
        );
    }
}
