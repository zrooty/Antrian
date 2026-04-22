<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Queue;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;
        if ($role === 'admin') {
            return redirect()->route('admin.rekap');
        } elseif ($role === 'petugas') {
            return redirect()->route('petugas.index');
        }
        
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
                ->with(['service', 'counter'])
                ->first();
            
            if ($activeQueue && $activeQueue->status === Queue::STATUS_WAITING) {
                $position = Queue::where('schedule_id', $schedule->id)
                    ->where('status', Queue::STATUS_WAITING)
                    ->where('id', '<', $activeQueue->id)
                    ->count() + 1;
                $estimasi = $position * 5;
            }
        }
        
        $doneQueue = null;
        if ($schedule && !$activeQueue) {
            $doneQueue = Queue::where('user_id', $user->id)
                ->where('schedule_id', $schedule->id)
                ->where('status', Queue::STATUS_DONE)
                ->with(['service', 'counter'])
                ->latest()
                ->first();
        }

        $historyQueues = Queue::where('user_id', $user->id)
            ->with(['service', 'counter', 'schedule'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('dashboard', compact('activeQueue', 'position', 'estimasi', 'doneQueue', 'historyQueues'));
    }

    public function status()
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();
        $schedule = Schedule::where('tanggal', $today)->first();
        
        if (!$schedule) return response()->json(['activeQueue' => null]);
        
        $activeQueue = Queue::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->whereNotIn('status', [Queue::STATUS_DONE])
            ->with(['service', 'counter', 'schedule'])
            ->first();
            
        $position = 0;
        $estimasi = 0;
        
        if ($activeQueue && $activeQueue->status === Queue::STATUS_WAITING) {
            $position = Queue::where('schedule_id', $schedule->id)
                ->where('status', Queue::STATUS_WAITING)
                ->where('id', '<', $activeQueue->id)
                ->count() + 1;
            $estimasi = $position * 5;
        }
        
        $historyQueues = Queue::where('user_id', $user->id)
            ->with(['service', 'counter', 'schedule'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return response()->json([
            'activeQueue' => $activeQueue,
            'position' => $position,
            'estimasi' => $estimasi,
            'historyQueues' => $historyQueues
        ]);
    }
}
