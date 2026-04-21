<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Queue;
use App\Models\Service;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

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
            'selesai' => Queue::whereDate('created_at', Carbon::today())->where('status', 'selesai')->count(),
            'menunggu' => Queue::whereDate('created_at', Carbon::today())->where('status', 'menunggu')->count(),
            'batal' => Queue::whereDate('created_at', Carbon::today())->where('status', 'batal')->count(),
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
     * Get data for DataTables.
     */
    public function getData()
    {
        $query = Queue::query()
            ->leftJoin('users', 'queues.user_id', '=', 'users.id')
            ->leftJoin('services', 'queues.service_id', '=', 'services.id')
            ->leftJoin('schedules', 'queues.schedule_id', '=', 'schedules.id')
            ->select([
                'queues.id',
                'users.name as nama_pasien',
                'services.nama_layanan',
                'queues.nomor_antrian',
                'queues.keluhan',
                'queues.status',
                'queues.created_at',
                'schedules.tanggal'
            ]);

        return DataTables::of($query)
            ->filterColumn('nama_pasien', function($query, $keyword) {
                $query->where('users.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('nama_layanan', function($query, $keyword) {
                $query->where('services.nama_layanan', 'like', "%{$keyword}%");
            })
            ->editColumn('created_at', function($row) {
                return $row->created_at->format('H:i:s');
            })
            ->editColumn('status', function($row) {
                $color = match($row->status) {
                    'menunggu' => 'yellow',
                    'dipanggil' => 'blue',
                    'selesai' => 'green',
                    'batal' => 'red',
                    default => 'gray'
                };
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-'.$color.'-100 text-'.$color.'-800">'.ucfirst($row->status).'</span>';
            })
            ->rawColumns(['status'])
            ->make(true);
    }
    /**
     * Get Report Data for DataTables (Historical)
     */
    public function getReportData(Request $request)
    {
        $query = Queue::query()
            ->leftJoin('users', 'queues.user_id', '=', 'users.id')
            ->leftJoin('services', 'queues.service_id', '=', 'services.id')
            ->select([
                'queues.id',
                'users.name as nama_pasien',
                'services.nama_layanan',
                'queues.nomor_antrian',
                'queues.status',
                'queues.created_at'
            ]);

        // Filter by date range if provided
        if ($request->filled('start_date')) {
            $query->whereDate('queues.created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('queues.created_at', '<=', $request->end_date);
        }

        return DataTables::of($query)
            ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y H:i'))
            ->editColumn('status', function($row) {
                $color = match($row->status) {
                    'selesai' => 'green',
                    'batal' => 'red',
                    default => 'gray'
                };
                return '<span class="px-2 py-0.5 text-[10px] font-bold rounded-lg bg-'.$color.'-100 text-'.$color.'-700 uppercase">'.$row->status.'</span>';
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    /**
     * Update Global Settings
     */
    public function updateSettings(Request $request)
    {
        $settings = $request->except('_token');
        
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
