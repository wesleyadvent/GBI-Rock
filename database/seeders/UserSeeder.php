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
            'nama' => 'Admin Sistem',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'id_bidang' => 1,
        ]);

        User::create([
            'nama' => 'Koordinator Bidang',
            'email' => 'koordinator@example.com',
            'password' => Hash::make('password'),
            'role' => 'koordinator_bidang',
            'id_bidang' => 2,
        ]);

        User::create([
            'nama' => 'Sekretaris',
            'email' => 'sekretaris@example.com',
            'password' => Hash::make('password'),
            'role' => 'sekretaris',
            'id_bidang' => 3,
        ]);

        User::create([
            'nama' => 'Pekerja',
            'email' => 'pekerja@example.com',
            'password' => Hash::make('password'),
            'role' => 'pekerja',
            'id_bidang' => 4,
        ]);

        User::create([
            'nama' => 'Penatua',
            'email' => 'penatua@example.com',
            'password' => Hash::make('password'),
            'role' => 'penatua',
            'id_bidang' => 5,
        ]);
    }
}
