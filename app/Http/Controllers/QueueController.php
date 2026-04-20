<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Queue;
use App\Models\Schedule;
use App\Events\PanggilAntrian;
use Carbon\Carbon;

class QueueController extends Controller
{
    /**
     * Display the officer dashboard with waiting queues.
     */
    public function petugasIndex()
    {
        $today = Carbon::today()->toDateString();
        $schedule = Schedule::where('tanggal', $today)->first();

        $queues = $schedule 
            ? Queue::where('schedule_id', $schedule->id)
                ->whereIn('status', ['menunggu', 'dipanggil'])
                ->orderBy('id', 'asc')
                ->get()
            : collect([]);

        return view('petugas.index', compact('queues'));
    }

    /**
     * Call the next queue.
     */
    public function panggil(Request $request, Queue $queue)
    {
        // Update status to dipanggil
        $queue->update(['status' => 'dipanggil']);

        // Misal loket diambil dari session atau hardcode dulu
        $loket = $request->input('loket', '1');

        // Broadcast event
        event(new PanggilAntrian($queue->nomor_antrian, $loket));

        return back()->with('status', 'Memanggil nomor ' . $queue->nomor_antrian);
    }

    /**
     * Display the TV screen.
     */
    public function tvIndex()
    {
        return view('tv.index');
    }
}
