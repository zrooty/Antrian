<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@antrian.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Petugas 1
        User::create([
            'name' => 'Budi Petugas',
            'email' => 'petugas@antrian.test',
            'password' => Hash::make('password'),
            'role' => 'petugas',
        ]);

        // Petugas 2
        User::create([
            'name' => 'Siti Petugas',
            'email' => 'petugas2@antrian.test',
            'password' => Hash::make('password'),
            'role' => 'petugas',
        ]);

        // Pasien Contoh
        User::create([
            'name' => 'Rizky Pasien',
            'email' => 'pasien@antrian.test',
            'password' => Hash::make('password'),
            'role' => 'pasien',
        ]);

        // Create 5 more random pasien using factory if available, 
        // but let's stick to manual for simplicity in this task.
        User::create([
            'name' => 'Andi',
            'email' => 'andi@example.com',
            'password' => Hash::make('password'),
            'role' => 'pasien',
        ]);
    }
}
