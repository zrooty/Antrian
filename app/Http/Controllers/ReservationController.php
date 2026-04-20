<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Show the form for creating a new reservation.
     */
    public function create()
    {
        $dates = [];
        // Loop 7 hari ke depan secara dinamis
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->addDays($i);
            $dates[$date->format('Y-m-d')] = $date->translatedFormat('l, d F Y');
        }

        $services = Service::all();

        return view('reservasi.create', compact('dates', 'services'));
    }

    /**
     * Store a newly created reservation in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'keluhan' => 'required|string|max:1000',
        ]);

        $date = $request->date;
        $service = Service::find($request->service_id);

        // 4a. Ambil atau buat Master Schedule untuk tanggal tersebut
        $schedule = Schedule::firstOrCreate(
            ['tanggal' => $date],
            ['kuota_maksimal' => 50] // Default kuota
        );

        // 4b. Logika Pengecekan Kuota (Validasi)
        $currentQueueCount = Queue::where('schedule_id', $schedule->id)->count();

        if ($currentQueueCount >= $schedule->kuota_maksimal) {
            return back()->withErrors(['date' => 'Maaf, kuota hari ini penuh.'])->withInput();
        }

        // 4c. Cegah Daftar Ganda
        $existingQueue = Queue::where('user_id', Auth::id())
            ->where('schedule_id', $schedule->id)
            ->where('status', 'menunggu')
            ->exists();

        if ($existingQueue) {
            return back()->withErrors(['date' => 'Anda telah memiliki antrian dengan status menunggu di tanggal ini.'])->withInput();
        }

        // 5. Generate Nomor Antrian (misal: A-001)
        // Hitung berapa banyak antrian di LAYANAN ini pada JADWAL ini
        $lastQueueNumber = Queue::where('schedule_id', $schedule->id)
            ->where('service_id', $service->id)
            ->count();
            
        $nextNumber = str_pad($lastQueueNumber + 1, 3, '0', STR_PAD_LEFT);
        $nomor_antrian = $service->kode_prefix . '-' . $nextNumber;

        // Simpan data antrian
        Queue::create([
            'user_id' => Auth::id(),
            'schedule_id' => $schedule->id,
            'service_id' => $service->id,
            'nomor_antrian' => $nomor_antrian,
            'keluhan' => $request->keluhan,
            'status' => 'menunggu',
        ]);

        return redirect()->route('dashboard')->with('status', 'Reservasi berhasil! Nomor antrian Anda: ' . $nomor_antrian);
    }
}
