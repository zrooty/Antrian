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
     * Display the admin rekap page.
     */
    public function rekapIndex()
    {
        return view('admin.rekap');
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
}
