<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Queue;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get current user's queue status.
     */
    public function status()
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();
        $schedule = Schedule::where('tanggal', $today)->first();
        
        $activeQueue = null;
        $position = 0;
        $estimasi = 0;

        if ($schedule) {
            $activeQueue = Queue::where('user_id', $user->id)
                ->where('schedule_id', $schedule->id)
                ->whereNotIn('status', [Queue::STATUS_DONE])
                ->with(['service', 'counter', 'schedule'])
                ->first();
                
            if ($activeQueue && $activeQueue->status === Queue::STATUS_WAITING) {
                $position = Queue::where('schedule_id', $schedule->id)
                    ->where('status', Queue::STATUS_WAITING)
                    ->where('id', '<', $activeQueue->id)
                    ->count() + 1;
                $estimasi = $position * 5;
            }
        }
        
        $historyQueues = Queue::where('user_id', $user->id)
            ->with(['service', 'counter', 'schedule'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'activeQueue' => $activeQueue,
                'position' => $position,
                'estimasi' => $estimasi,
                'historyQueues' => $historyQueues->items(),
                'pagination' => (string) $historyQueues->links()
            ]
        ]);
    }
}
