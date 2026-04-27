<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::latest()->paginate(10);
        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'kode_prefix' => 'required|string|max:5|unique:services',
        ]);

        Service::create($validated);
        
        // Log activity
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE_SERVICE',
            'description' => "Admin menambahkan layanan baru: {$validated['nama_layanan']} ({$validated['kode_prefix']}).",
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'kode_prefix' => 'required|string|max:5|unique:services,kode_prefix,' . $service->id,
        ]);

        $service->update($validated);
        
        // Log activity
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_SERVICE',
            'description' => "Admin memperbarui layanan: {$service->nama_layanan} ({$service->kode_prefix}).",
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $serviceName = $service->nama_layanan;
        $servicePrefix = $service->kode_prefix;
        $service->delete();

        // Log activity
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE_SERVICE',
            'description' => "Admin menghapus layanan: {$serviceName} ({$servicePrefix}).",
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dihapus.');
    }
}
