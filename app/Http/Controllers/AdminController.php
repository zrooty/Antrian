<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Queue;
use App\Models\Service;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display the admin rekap/dashboard page.
     */
    public function rekapIndex()
    {
        // Simple stats for dashboard
        $stats = [
            'total_antrian' => Queue::whereDate('created_at', Carbon::today())->count(),
            'selesai' => Queue::whereDate('created_at', Carbon::today())->where('status', Queue::STATUS_DONE)->count(),
            'menunggu' => Queue::whereDate('created_at', Carbon::today())->where('status', Queue::STATUS_WAITING)->count(),
            'batal' => Queue::whereDate('created_at', Carbon::today())->where('status', Queue::STATUS_SKIPPED)->count(),
        ];
        
        return view('admin.rekap', compact('stats'));
    }

    /**
     * Monitoring view
     */
    public function monitoringIndex()
    {
        return view('admin.monitoring');
    }

    /**
     * Operational view
     */
    public function operationalIndex()
    {
        return view('admin.operational');
    }

    /**
     * Reset Queue Action
     */
    public function resetQueue(Request $request)
    {
        Queue::whereDate('created_at', Carbon::today())->delete();
        
        // Log activity
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'RESET_QUEUE',
            'description' => 'Admin me-reset antrian hari ini.',
        ]);

        return back()->with('success', 'Antrian hari ini telah di-reset.');
    }

    /**
     * Report view
     */
    public function reportIndex()
    {
        return view('admin.reports');
    }

    /**
     * Settings view
     */
    public function settingIndex()
    {
        return view('admin.settings');
    }

    /**
     * Log Activity view
     */
    public function logIndex()
    {
        $logs = \App\Models\ActivityLog::with('user')->latest()->paginate(20);
        return view('admin.logs', compact('logs'));
    }

    /**
     * Update Global Settings
     */
    public function updateSettings(Request $request)
    {
        // Daftar key settings yang diizinkan
        $allowedKeys = [
            'app_name',
            'app_slogan',
            'max_queue_per_day',
            'queue_prefix_mode',
        ];

        // Validasi input
        $request->validate([
            'app_name'          => 'nullable|string|max:255',
            'app_slogan'        => 'nullable|string|max:255',
            'max_queue_per_day'  => 'nullable|integer|min:0',
            'queue_prefix_mode'  => 'nullable|string|in:per_service,global',
        ]);

        // Hanya proses key yang ada di whitelist
        $settings = $request->only($allowedKeys);
        
        foreach ($settings as $key => $value) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Log activity
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_SETTINGS',
            'description' => 'Admin memperbarui konfigurasi sistem.',
        ]);

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }

}
