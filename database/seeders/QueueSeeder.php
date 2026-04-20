<?php

namespace Database\Seeders;

use App\Models\Queue;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class QueueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today()->toDateString();
        $user = User::where('role', 'pasien')->first();
        $service = Service::first();

        if (!$user || !$service) {
            return;
        }

        // 1. Pastikan ada Schedule hari ini
        $schedule = Schedule::firstOrCreate(
            ['tanggal' => $today],
            ['kuota_maksimal' => 50]
        );

        // 2. Buat data antrian untuk hari ini
        $antrianData = [
            ['nomor' => 'A-001', 'keluhan' => 'Sakit Gigi'],
            ['nomor' => 'A-002', 'keluhan' => 'Kontrol Rutin'],
            ['nomor' => 'A-003', 'keluhan' => 'Gigi Berlubang'],
        ];

        foreach ($antrianData as $data) {
            Queue::create([
                'user_id' => $user->id,
                'schedule_id' => $schedule->id,
                'service_id' => $service->id,
                'nomor_antrian' => $data['nomor'],
                'keluhan' => $data['keluhan'],
                'status' => 'menunggu',
            ]);
        }
    }
}
