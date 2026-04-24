<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Queue;
use App\Models\Counter;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    /**
     * Get live monitoring data.
     */
    public function monitoring()
    {
        $today = Carbon::today()->toDateString();
        $schedule = Schedule::where('tanggal', $today)->first();

        $counters = Counter::where('status', 'active')->get()->map(function ($counter) use ($schedule) {
            $activeQueue = null;
            if ($schedule) {
                $activeQueue = Queue::where('schedule_id', $schedule->id)
                    ->where('counter_id', $counter->id)
                    ->whereIn('status', [Queue::STATUS_CALLED, Queue::STATUS_PROCESSING])
                    ->with(['user', 'service'])
                    ->first();
            }

            return [
                'id' => $counter->id,
                'name' => $counter->name,
                'code' => $counter->code,
                'active_queue' => $activeQueue ? [
                    'nomor_antrian' => $activeQueue->nomor_antrian,
                    'status' => $activeQueue->status,
                    'patient_name' => $activeQueue->user->name ?? 'Pasien',
                    'service_name' => $activeQueue->service->nama_layanan ?? '-',
                ] : null,
            ];
        });

        $waitingQueues = collect();
        if ($schedule) {
            $waitingQueues = Queue::where('schedule_id', $schedule->id)
                ->where('status', Queue::STATUS_WAITING)
                ->with(['user', 'service'])
                ->oldest('created_at')
                ->take(20)
                ->get()
                ->map(function ($queue) {
                    return [
                        'id' => $queue->id,
                        'nomor_antrian' => $queue->nomor_antrian,
                        'patient_name' => $queue->user->name ?? 'Pasien',
                        'service_name' => $queue->service->nama_layanan ?? '-',
                        'time' => $queue->created_at->format('H:i'),
                    ];
                });
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'counters' => $counters,
                'waiting_queues' => $waitingQueues,
            ]
        ]);
    }

    /**
     * Get statistics data for dashboard.
     */
    public function stats()
    {
        $query = Queue::query()
            ->leftJoin('users', 'queues.user_id', '=', 'users.id')
            ->leftJoin('services', 'queues.service_id', '=', 'services.id')
            ->whereDate('queues.created_at', Carbon::today())
            ->select([
                'queues.id',
                'users.name as nama_pasien',
                'services.nama_layanan',
                'queues.nomor_antrian',
                'queues.status',
                'queues.created_at'
            ]);

        return DataTables::of($query)
            ->editColumn('created_at', fn($row) => $row->created_at->format('H:i:s'))
            ->editColumn('status', function($row) {
                $color = match($row->status) {
                    Queue::STATUS_WAITING => 'yellow',
                    Queue::STATUS_CALLED, Queue::STATUS_PROCESSING => 'blue',
                    Queue::STATUS_DONE => 'green',
                    Queue::STATUS_SKIPPED => 'red',
                    default => 'gray'
                };
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-'.$color.'-100 text-'.$color.'-800">'.ucfirst($row->status).'</span>';
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    /**
     * Get report data (historical).
     */
    public function reports(Request $request)
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
                    'done' => 'green',
                    'skipped' => 'red',
                    default => 'gray'
                };
                return '<span class="px-2 py-0.5 text-[10px] font-bold rounded-lg bg-'.$color.'-100 text-'.$color.'-700 uppercase">'.$row->status.'</span>';
            })
            ->rawColumns(['status'])
            ->make(true);
    }
}
