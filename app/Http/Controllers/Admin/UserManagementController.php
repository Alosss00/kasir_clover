<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserManagementController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna (Admin & Responden).
     */
    public function index(Request $request)
    {
        $roles = Role::all();
        $permissions = Permission::all();

        $query = User::with(['roles', 'permissions']);

        // Filter Pencarian (Nama / Email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter Status Aktif / Nonaktif
        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('is_active', (bool) $request->status);
        }

        // Filter Berdasarkan Role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->latest()->paginate(10);

        return view('admin.users.index', compact('users', 'roles', 'permissions'));
    }

    /**
     * Tampilkan form tambah pengguna baru oleh Admin.
     */
    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.users.create', compact('roles', 'permissions'));
    }

    /**
     * Simpan pengguna baru ke database beserta Role & Hak Akses (Permissions).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::defaults()],
            'is_active' => ['boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $selectedRoles = $request->roles ?? [];
        
        // Otomatis tambahkan role dasar 'Responden' jika memilih salah satu role SML
        $smlRolesChosen = array_filter($selectedRoles, fn($r) => str_contains($r, 'Responden -'));
        if (!empty($smlRolesChosen) && !in_array('Responden', $selectedRoles)) {
            $selectedRoles[] = 'Responden';
        }

        if (!empty($selectedRoles)) {
            $user->syncRoles($selectedRoles);
        }

        if (!empty($request->permissions)) {
            $user->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna baru '{$user->name}' dengan role & hak akses terpilih berhasil ditambahkan.");
    }

    /**
     * Tampilkan form edit pengguna.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        $userPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'permissions', 'userRoles', 'userPermissions'));
    }

    /**
     * Update data pengguna, status, password, roles, dan hak akses khusus (permissions).
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', Password::defaults()],
            'is_active' => ['boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        // Mencegah Admin menonaktifkan akun sendiri yang sedang login
        if ($user->id === Auth::id() && !$request->boolean('is_active')) {
            return back()->withInput()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang digunakan.');
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        $selectedRoles = $request->roles ?? [];

        // Proteksi: Admin tidak bisa mencabut role 'Admin' dari akunnya sendiri saat mengedit profil sendiri
        if ($user->id === Auth::id() && !in_array('Admin', $selectedRoles)) {
            $selectedRoles[] = 'Admin';
        }

        // Otomatis sertakan role dasar 'Responden' jika memilih role SML
        $smlRolesChosen = array_filter($selectedRoles, fn($r) => str_contains($r, 'Responden -'));
        if (!empty($smlRolesChosen) && !in_array('Responden', $selectedRoles)) {
            $selectedRoles[] = 'Responden';
        }

        // Sinkronisasi Roles & Direct Permissions
        $user->syncRoles($selectedRoles);
        $user->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.users.index')
            ->with('success', "Data, role, dan hak akses pengguna '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Toggle status aktif/nonaktif akun user (Fast Action).
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang digunakan.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusMessage = $user->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';

        return back()->with('success', "Status akun '{$user->name}' berhasil {$statusMessage}.");
    }

    /**
     * Hapus akun pengguna dari database.
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna '{$name}' telah berhasil dihapus.");
    }
}
