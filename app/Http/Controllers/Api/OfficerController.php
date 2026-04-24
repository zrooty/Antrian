<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Queue;
use App\Models\Schedule;
use App\Events\PanggilAntrian;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OfficerController extends Controller
{
    /**
     * Get updated dashboard data for officer.
     */
    public function dashboard()
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();
        $schedule = Schedule::where('tanggal', $today)->first();

        if (!$schedule) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada jadwal hari ini.',
                'data' => [
                    'waitingQueues' => [],
                    'totalWaiting' => 0,
                    'pagination' => '',
                    'activeQueueHtml' => '',
                    'skippedQueuesHtml' => '',
                    'handledQueuesHtml' => '',
                ]
            ]);
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

        return response()->json([
            'status' => 'success',
            'data' => [
                'waitingQueues' => $waitingQueues->items(),
                'totalWaiting' => $waitingQueues->total(),
                'pagination' => (string) $waitingQueues->links(),
                'activeQueueHtml' => view('petugas.partials.active-queue', ['activeQueue' => $activeQueue])->render(),
                'skippedQueuesHtml' => view('petugas.partials.skipped-list', ['skippedQueues' => $skippedQueues])->render(),
                'handledQueuesHtml' => view('petugas.partials.handled-list', ['handledQueues' => $handledQueues])->render(),
            ]
        ]);
    }

    /**
     * Call the next queue.
     */
    public function call(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();
        $schedule = Schedule::where('tanggal', $today)->first();

        if (!$schedule) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada jadwal hari ini.'], 422);
        }

        $hasActive = Queue::where('schedule_id', $schedule->id)
            ->where('counter_id', $user->counter_id)
            ->whereIn('status', ['called', 'processing'])
            ->exists();

        if ($hasActive) {
            return response()->json(['status' => 'error', 'message' => 'Selesaikan antrian aktif terlebih dahulu.'], 422);
        }

        $nextQueue = Queue::where('schedule_id', $schedule->id)
            ->where('status', 'waiting')
            ->orderBy('id', 'asc')
            ->first();

        if (!$nextQueue) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada antrian menunggu.'], 422);
        }

        $nextQueue->update([
            'status' => 'called',
            'counter_id' => $user->counter_id
        ]);

        event(new PanggilAntrian($nextQueue->nomor_antrian, $user->counter?->name ?? 'Loket'));

        return response()->json([
            'status' => 'success',
            'message' => 'Memanggil nomor ' . $nextQueue->nomor_antrian
        ]);
    }

    /**
     * Start processing a queue.
     */
    public function start(Queue $queue)
    {
        if ($queue->status !== 'called') {
            return response()->json(['status' => 'error', 'message' => 'Antrian tidak dalam status dipanggil.'], 422);
        }

        $queue->update(['status' => 'processing']);

        return response()->json([
            'status' => 'success',
            'message' => 'Pelayanan dimulai untuk nomor ' . $queue->nomor_antrian
        ]);
    }

    /**
     * Finish a queue.
     */
    public function finish(Queue $queue)
    {
        if ($queue->status !== 'processing') {
            return response()->json(['status' => 'error', 'message' => 'Antrian tidak sedang diproses.'], 422);
        }

        $queue->update(['status' => 'done']);

        return response()->json([
            'status' => 'success',
            'message' => 'Pelayanan selesai untuk nomor ' . $queue->nomor_antrian
        ]);
    }

    /**
     * Skip a queue.
     */
    public function skip(Queue $queue)
    {
        if ($queue->status !== 'called') {
            return response()->json(['status' => 'error', 'message' => 'Hanya antrian yang dipanggil yang dapat dilewati.'], 422);
        }

        $queue->update(['status' => 'skipped']);

        return response()->json([
            'status' => 'success',
            'message' => 'Nomor ' . $queue->nomor_antrian . ' dilewati.'
        ]);
    }

    /**
     * Recall a skipped queue.
     */
    public function recall(Queue $queue)
    {
        if ($queue->status !== 'skipped') {
            return response()->json(['status' => 'error', 'message' => 'Hanya antrian yang terlewati yang dapat dipanggil ulang.'], 422);
        }

        $hasActive = Queue::where('schedule_id', $queue->schedule_id)
            ->where('counter_id', auth()->user()->counter_id)
            ->whereIn('status', ['called', 'processing'])
            ->exists();

        if ($hasActive) {
            return response()->json(['status' => 'error', 'message' => 'Selesaikan antrian aktif terlebih dahulu.'], 422);
        }

        $queue->update(['status' => 'called']);

        event(new PanggilAntrian($queue->nomor_antrian, auth()->user()->counter?->name ?? 'Loket'));

        return response()->json([
            'status' => 'success',
            'message' => 'Memanggil ulang nomor ' . $queue->nomor_antrian
        ]);
    }
}
