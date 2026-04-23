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
     * Get updated dashboard content for AJAX.
     */
    public function getPetugasData()
    {
        $user = auth()->user();
        $data = $this->getDashboardData($user);
        
        return response()->json([
            'waitingQueues' => $data['waitingQueues']->items(),
            'totalWaiting' => $data['waitingQueues']->total(),
            'pagination' => (string) $data['waitingQueues']->links(),
            'activeQueueHtml' => view('petugas.partials.active-queue', ['activeQueue' => $data['activeQueue']])->render(),
            'skippedQueuesHtml' => view('petugas.partials.skipped-list', ['skippedQueues' => $data['skippedQueues']])->render(),
            'handledQueuesHtml' => view('petugas.partials.handled-list', ['handledQueues' => $data['handledQueues']])->render(),
        ]);
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

        // Skipped queues (typically small enough for today at one counter)
        $skippedQueues = Queue::where('schedule_id', $schedule->id)
            ->where('status', 'skipped')
            ->where('counter_id', $user->counter_id)
            ->latest('updated_at')
            ->get();

        // Handled queues for today at this counter
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
            if ($request->ajax()) return response()->json(['error' => 'Selesaikan antrian aktif terlebih dahulu.'], 422);
            return back()->with('error', 'Selesaikan antrian aktif terlebih dahulu.');
        }

        $nextQueue = Queue::where('schedule_id', $schedule->id)
            ->where('status', 'waiting')
            ->orderBy('id', 'asc')
            ->first();

        if (!$nextQueue) {
            if ($request->ajax()) return response()->json(['error' => 'Tidak ada antrian menunggu.'], 422);
            return back()->with('error', 'Tidak ada antrian menunggu.');
        }

        $nextQueue->update([
            'status' => 'called',
            'counter_id' => $user->counter_id
        ]);

        // Broadcast event
        event(new PanggilAntrian($nextQueue->nomor_antrian, $user->counter?->name ?? 'Loket'));

        $msg = 'Memanggil nomor ' . $nextQueue->nomor_antrian;
        if ($request->ajax()) return response()->json(['status' => $msg]);
        return back()->with('status', $msg);
    }

    /**
     * Start processing the current called queue.
     */
    public function startProcessing(Queue $queue)
    {
        if ($queue->status !== 'called') {
            if (request()->ajax()) return response()->json(['error' => 'Antrian tidak dalam status dipanggil.'], 422);
            return back()->with('error', 'Antrian tidak dalam status dipanggil.');
        }

        $queue->update(['status' => 'processing']);

        $msg = 'Pelayanan dimulai untuk nomor ' . $queue->nomor_antrian;
        if (request()->ajax()) return response()->json(['status' => $msg]);
        return back()->with('status', $msg);
    }

    /**
     * Finish the current processing queue.
     */
    public function finishQueue(Queue $queue)
    {
        if ($queue->status !== 'processing') {
            if (request()->ajax()) return response()->json(['error' => 'Antrian tidak sedang diproses.'], 422);
            return back()->with('error', 'Antrian tidak sedang diproses.');
        }

        $queue->update(['status' => 'done']);

        $msg = 'Pelayanan selesai untuk nomor ' . $queue->nomor_antrian;
        if (request()->ajax()) return response()->json(['status' => $msg]);
        return back()->with('status', $msg);
    }

    /**
     * Skip the current called queue.
     */
    public function skipQueue(Queue $queue)
    {
        if ($queue->status !== 'called') {
            if (request()->ajax()) return response()->json(['error' => 'Hanya antrian yang dipanggil yang dapat dilewati.'], 422);
            return back()->with('error', 'Hanya antrian yang dipanggil yang dapat dilewati.');
        }

        $queue->update(['status' => 'skipped']);

        $msg = 'Nomor ' . $queue->nomor_antrian . ' dilewati.';
        if (request()->ajax()) return response()->json(['status' => $msg]);
        return back()->with('status', $msg);
    }

    /**
     * Recall a skipped queue.
     */
    public function recallQueue(Queue $queue)
    {
        if ($queue->status !== 'skipped') {
            if (request()->ajax()) return response()->json(['error' => 'Hanya antrian yang terlewati yang dapat dipanggil ulang.'], 422);
            return back()->with('error', 'Hanya antrian yang terlewati yang dapat dipanggil ulang.');
        }

        // Check if there's other active queue
        $hasActive = Queue::where('schedule_id', $queue->schedule_id)
            ->whereIn('status', ['called', 'processing'])
            ->exists();

        if ($hasActive) {
            if (request()->ajax()) return response()->json(['error' => 'Selesaikan antrian aktif terlebih dahulu.'], 422);
            return back()->with('error', 'Selesaikan antrian aktif terlebih dahulu.');
        }

        $queue->update(['status' => 'called']);

        // Broadcast event
        event(new PanggilAntrian($queue->nomor_antrian, auth()->user()->counter?->name ?? 'Loket'));

        $msg = 'Memanggil ulang nomor ' . $queue->nomor_antrian;
        if (request()->ajax()) return response()->json(['status' => $msg]);
        return back()->with('status', $msg);
    }

    /**
     * Display the TV screen.
     */
    public function tvIndex()
    {
        return view('tv.index');
    }
}
