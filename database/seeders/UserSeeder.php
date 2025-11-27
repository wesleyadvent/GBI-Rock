<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seed.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Koordinator Bidang',
            'email' => 'koordinator@example.com',
            'password' => Hash::make('password'),
            'role' => 'koordinator_bidang',
        ]);

        User::create([
            'name' => 'Sekretaris',
            'email' => 'sekretaris@example.com',
            'password' => Hash::make('password'),
            'role' => 'sekretaris',
        ]);

        User::create([
            'name' => 'Pekerja',
            'email' => 'pekerja@example.com',
            'password' => Hash::make('password'),
            'role' => 'pekerja',
        ]);

        User::create([
            'name' => 'Penatua',
            'email' => 'penatua@example.com',
            'password' => Hash::make('password'),
            'role' => 'penatua',
        ]);
    }
}
