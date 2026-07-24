<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Web',
            'email' => 'adminweb@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin_web',
        ]);

        User::create([
            'name' => 'Admin Tour',
            'email' => 'admintour@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin_tour',
        ]);
    }
}
