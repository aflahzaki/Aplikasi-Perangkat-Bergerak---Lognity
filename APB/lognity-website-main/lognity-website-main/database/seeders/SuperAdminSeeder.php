<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        User::create([
            'username' => 'SuperAdmin',
            'email' => 'super@ascendia.com',
            'password' => Hash::make('password123'), // Ganti password kuat
            'role' => 'Superadmin',
            'current_level' => 'Artefak',
            'points' => 99999,
        ]);
    }
}
