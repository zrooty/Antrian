<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use Illuminate\Http\Request;

class CounterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $counters = Counter::latest()->paginate(10);
        return view('admin.counters.index', compact('counters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.counters.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:counters',
            'status' => 'required|in:active,inactive',
        ]);

        Counter::create($validated);
        
        // Log activity
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE_COUNTER',
            'description' => "Admin menambahkan loket baru: {$validated['name']} ({$validated['code']}).",
        ]);

        return redirect()->route('admin.counters.index')->with('success', 'Loket berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Counter $counter)
    {
        return view('admin.counters.show', compact('counter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Counter $counter)
    {
        return view('admin.counters.edit', compact('counter'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Counter $counter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:counters,code,' . $counter->id,
            'status' => 'required|in:active,inactive',
        ]);

        $counter->update($validated);
        
        // Log activity
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_COUNTER',
            'description' => "Admin memperbarui loket: {$counter->name} ({$counter->code}).",
        ]);

        return redirect()->route('admin.counters.index')->with('success', 'Loket berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Counter $counter)
    {
        $counterName = $counter->name;
        $counterCode = $counter->code;
        $counter->delete();

        // Log activity
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE_COUNTER',
            'description' => "Admin menghapus loket: {$counterName} ({$counterCode}).",
        ]);

        return redirect()->route('admin.counters.index')->with('success', 'Loket berhasil dihapus.');
    }
}
