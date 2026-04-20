<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Service::create([
            'nama_layanan' => 'Poli Umum',
            'kode_prefix' => 'A',
        ]);

        \App\Models\Service::create([
            'nama_layanan' => 'Poli Gigi',
            'kode_prefix' => 'B',
        ]);

        \App\Models\Service::create([
            'nama_layanan' => 'Poli Anak',
            'kode_prefix' => 'C',
        ]);
    }
}
