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

        $today = Carbon::today()->toDateString();
        $schedule = Schedule::where('tanggal', $today)->first();

        if (!$schedule) {
            return view('petugas.index', [
                'waitingQueues' => collect([]),
                'activeQueue' => null,
                'skippedQueues' => collect([]),
                'handledQueues' => collect([]),
                'counter' => $user->counter
            ]);
        }

        // Get queues for today
        $queues = Queue::where('schedule_id', $schedule->id)->get();

        // Active queue for this specific counter
        $activeQueue = Queue::where('schedule_id', $schedule->id)
            ->whereIn('status', ['called', 'processing'])
            ->where('counter_id', $user->counter_id)
            ->latest('updated_at')
            ->first();

        // Handled queues for today at this counter
        $handledQueues = Queue::where('schedule_id', $schedule->id)
            ->where('counter_id', $user->counter_id)
            ->where('status', 'done')
            ->latest('updated_at')
            ->get();

        return view('petugas.index', [
            'waitingQueues' => $queues->where('status', 'waiting')->sortBy('id'),
            'activeQueue' => $activeQueue,
            'skippedQueues' => $queues->where('status', 'skipped')->where('counter_id', $user->counter_id)->sortByDesc('updated_at'),
            'handledQueues' => $handledQueues,
            'counter' => $user->counter
        ]);
    }

    /**
     * Call the next queue.
     */
    public function panggil(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();
        $schedule = Schedule::where('tanggal', $today)->first();

        if (!$schedule) return back()->with('error', 'Tidak ada jadwal hari ini.');

        // Check if there is already an active queue being processed at THIS counter
        $hasActive = Queue::where('schedule_id', $schedule->id)
            ->where('counter_id', $user->counter_id)
            ->whereIn('status', ['called', 'processing'])
            ->exists();

        if ($hasActive) {
            return back()->with('error', 'Selesaikan antrian aktif terlebih dahulu.');
        }

        $nextQueue = Queue::where('schedule_id', $schedule->id)
            ->where('status', 'waiting')
            ->orderBy('id', 'asc')
            ->first();

        if (!$nextQueue) {
            return back()->with('error', 'Tidak ada antrian menunggu.');
        }

        $nextQueue->update([
            'status' => 'called',
            'counter_id' => $user->counter_id
        ]);

        // Broadcast event
        event(new PanggilAntrian($nextQueue->nomor_antrian, $user->counter?->name ?? 'Loket'));

        return back()->with('status', 'Memanggil nomor ' . $nextQueue->nomor_antrian);
    }

    /**
     * Start processing the current called queue.
     */
    public function startProcessing(Queue $queue)
    {
        if ($queue->status !== 'called') {
            return back()->with('error', 'Antrian tidak dalam status dipanggil.');
        }

        $queue->update(['status' => 'processing']);

        return back()->with('status', 'Pelayanan dimulai untuk nomor ' . $queue->nomor_antrian);
    }

    /**
     * Finish the current processing queue.
     */
    public function finishQueue(Queue $queue)
    {
        if ($queue->status !== 'processing') {
            return back()->with('error', 'Antrian tidak sedang diproses.');
        }

        $queue->update(['status' => 'done']);

        return back()->with('status', 'Pelayanan selesai untuk nomor ' . $queue->nomor_antrian);
    }

    /**
     * Skip the current called queue.
     */
    public function skipQueue(Queue $queue)
    {
        if ($queue->status !== 'called') {
            return back()->with('error', 'Hanya antrian yang dipanggil yang dapat dilewati.');
        }

        $queue->update(['status' => 'skipped']);

        return back()->with('status', 'Nomor ' . $queue->nomor_antrian . ' dilewati.');
    }

    /**
     * Recall a skipped queue.
     */
    public function recallQueue(Queue $queue)
    {
        if ($queue->status !== 'skipped') {
            return back()->with('error', 'Hanya antrian yang terlewati yang dapat dipanggil ulang.');
        }

        // Check if there's other active queue
        $hasActive = Queue::where('schedule_id', $queue->schedule_id)
            ->whereIn('status', ['called', 'processing'])
            ->exists();

        if ($hasActive) {
            return back()->with('error', 'Selesaikan antrian aktif terlebih dahulu.');
        }

        $queue->update(['status' => 'called']);

        // Broadcast event
        event(new PanggilAntrian($queue->nomor_antrian, auth()->user()->counter?->name ?? 'Loket'));

        return back()->with('status', 'Memanggil ulang nomor ' . $queue->nomor_antrian);
    }

    /**
     * Display the TV screen.
     */
    public function tvIndex()
    {
        return view('tv.index');
    }
}
