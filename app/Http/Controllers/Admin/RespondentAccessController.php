<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RespondentAccessController extends Controller
{
    /**
     * Tampilkan daftar user responden dan role SML yang dimiliki.
     */
    public function index(Request $request)
    {
        // Ambil semua role kecuali 'Admin' untuk filter/pilihan
        $roles = Role::where('name', '!=', 'Admin')->get();

        // Ambil user yang memiliki role Responden atau role SML apapun (atau non-admin)
        $usersQuery = User::with('roles')->whereDoesntHave('roles', function ($query) {
            $query->where('name', 'Admin');
        });

        if ($request->filled('role')) {
            $usersQuery->role($request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->latest()->paginate(10);

        return view('admin.respondents.index', compact('users', 'roles'));
    }

    /**
     * Form edit hak akses / role SML user responden.
     */
    public function edit(User $user)
    {
        // Pastikan tidak mengedit Admin melalui halaman ini
        if ($user->hasRole('Admin')) {
            return redirect()->route('admin.respondents.index')
                ->with('error', 'User Admin tidak dapat diubah melalui menu responden.');
        }

        $roles = Role::where('name', '!=', 'Admin')->get();
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('admin.respondents.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update role (1 atau lebih) yang dimiliki oleh responden.
     */
    public function update(Request $request, User $user)
    {
        if ($user->hasRole('Admin')) {
            return redirect()->route('admin.respondents.index')
                ->with('error', 'User Admin tidak dapat diubah melalui menu responden.');
        }

        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $selectedRoles = $request->roles ?? [];

        // Pastikan role dasar 'Responden' selalu disertakan jika memilih role SML
        if (!empty($selectedRoles) && !in_array('Responden', $selectedRoles)) {
            $selectedRoles[] = 'Responden';
        }

        // Sinkronisasi roles pengguna
        $user->syncRoles($selectedRoles);

        return redirect()->route('admin.respondents.index')
            ->with('success', "Berhasil memperbarui hak akses & tipe responden untuk user {$user->name}.");
    }

    /**
     * Menambahkan Tipe Responden SML Baru (Role Baru) jika ada Kepdirjen baru.
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create(['name' => trim($request->name)]);

        return redirect()->route('admin.respondents.index')
            ->with('success', "Tipe Responden SML baru ('{$request->name}') berhasil ditambahkan.");
    }
}
