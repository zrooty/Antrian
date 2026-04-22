<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Counter;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $counters = Counter::where('status', 'active')->get();
        return view('admin.users.create', compact('counters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:pasien,petugas,admin',
            'counter_id' => [
                'required_if:role,petugas',
                'nullable',
                'exists:counters,id,status,active'
            ],
        ], [
            'counter_id.required_if' => 'Loket wajib dipilih jika role adalah petugas.',
            'counter_id.exists' => 'Loket yang dipilih tidak valid atau tidak aktif.',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        // Jika bukan petugas, pastikan counter_id null
        if ($validated['role'] !== 'petugas') {
            $validated['counter_id'] = null;
        }

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $counters = Counter::where('status', 'active')->get();
        return view('admin.users.edit', compact('user', 'counters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:pasien,petugas,admin',
            'counter_id' => [
                'required_if:role,petugas',
                'nullable',
                'exists:counters,id,status,active'
            ],
        ], [
            'counter_id.required_if' => 'Loket wajib dipilih jika role adalah petugas.',
            'counter_id.exists' => 'Loket yang dipilih tidak valid atau tidak aktif.',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'confirmed|min:8']);
            $validated['password'] = bcrypt($request->password);
        }

        // Jika bukan petugas, pastikan counter_id null
        if ($validated['role'] !== 'petugas') {
            $validated['counter_id'] = null;
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
