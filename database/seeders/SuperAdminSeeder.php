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
            ['email' => 'kurniawankrisna803@gmail.com'],
            [
                'name' => 'Krisna',
                'password' => Hash::make('Krisnakrnwnn803'),
                'role' => 'Super Admin',
            ]
        );
    }
}
