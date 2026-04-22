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
        // --- Akun Utama (Statis) ---

        // Admin
        User::create([
            'name' => 'Administrator UAT',
            'email' => 'admin.uat@antrian.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Petugas Loket 1
        $counter1 = \App\Models\Counter::where('code', 'A')->first();
        User::create([
            'name' => 'Petugas Loket 1',
            'email' => 'petugas1.uat@antrian.test',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'counter_id' => $counter1->id ?? null,
        ]);

        // Petugas Loket 2
        $counter2 = \App\Models\Counter::where('code', 'B')->first();
        User::create([
            'name' => 'Petugas Loket 2',
            'email' => 'petugas2.uat@antrian.test',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'counter_id' => $counter2->id ?? null,
        ]);

        // --- Akun Pasien Test (Statis) ---

        User::create([
            'name' => 'Pasien Test 1',
            'email' => 'pasien1.uat@antrian.test',
            'password' => Hash::make('password'),
            'role' => 'pasien',
        ]);

        User::create([
            'name' => 'Pasien Test 2',
            'email' => 'pasien2.uat@antrian.test',
            'password' => Hash::make('password'),
            'role' => 'pasien',
        ]);

        // --- Akun Pasien Dummy (Dinamis) ---

        User::factory(20)->create([
            'role' => 'pasien',
        ]);
    }
}
