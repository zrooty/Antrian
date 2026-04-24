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
     * Display the officer dashboard.
     */
    public function petugasIndex()
    {
        $user = auth()->user();
        if (!$user->counter_id && $user->role !== 'admin') {
            return redirect('/')->with('error', 'Anda belum ditugaskan ke loket manapun. Silakan hubungi Admin.');
        }

        $data = $this->getDashboardData($user);
        $data['counter'] = $user->counter;

        return view('petugas.index', $data);
    }

    /**
     * Common data fetching logic for officer dashboard.
     */
    private function getDashboardData($user)
    {
        $today = Carbon::today()->toDateString();
        $schedule = Schedule::where('tanggal', $today)->first();

        if (!$schedule) {
            return [
                'waitingQueues' => collect([]),
                'activeQueue' => null,
                'skippedQueues' => collect([]),
                'handledQueues' => collect([]),
            ];
        }

        // Active queue for this specific counter
        $activeQueue = Queue::where('schedule_id', $schedule->id)
            ->whereIn('status', ['called', 'processing'])
            ->where('counter_id', $user->counter_id)
            ->latest('updated_at')
            ->first();

        // Waiting queues with pagination
        $waitingQueues = Queue::where('schedule_id', $schedule->id)
            ->where('status', 'waiting')
            ->orderBy('id', 'asc')
            ->paginate(10);

        // Skipped queues
        $skippedQueues = Queue::where('schedule_id', $schedule->id)
            ->where('status', 'skipped')
            ->where('counter_id', $user->counter_id)
            ->latest('updated_at')
            ->get();

        // Handled queues
        $handledQueues = Queue::where('schedule_id', $schedule->id)
            ->where('counter_id', $user->counter_id)
            ->where('status', 'done')
            ->latest('updated_at')
            ->get();

        return [
            'waitingQueues' => $waitingQueues,
            'activeQueue' => $activeQueue,
            'skippedQueues' => $skippedQueues,
            'handledQueues' => $handledQueues,
        ];
    }

    /**
     * Display the TV screen.
     */
    public function tvIndex()
    {
        return view('tv.index');
    }
}
