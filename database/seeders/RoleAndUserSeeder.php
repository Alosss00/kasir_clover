<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Permissions (Hak Akses Fitur)
        $permissions = [
            'kelola-survei',  // Membuat & mengedit survei
            'rekap-survei',   // Melihat analisis/grafik survei
            'kelola-user',    // Mengelola user, status, & role
            'isi-survei',     // Mengisi survei responden
        ];

        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName]);
        }

        // 2. Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $respondenBaseRole = Role::firstOrCreate(['name' => 'Responden']);

        // Berikan Hak Akses ke Role Admin
        $adminRole->syncPermissions(Permission::all());

        // Berikan Hak Akses ke Role Responden
        $respondenBaseRole->syncPermissions(['isi-survei']);

        // Tipe Responden SML Kepdirjen
        $smlRoles = [
            'Responden - Produsen UTTP',
            'Responden - Importir UTTP',
            'Responden - Reparatur UTTP',
            'Responden - Pemilik/Pengguna UTTP',
            'Responden - Instansi Pemerintah',
            'Responden - Masyarakat Umum',
        ];

        foreach ($smlRoles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions(['isi-survei']);
        }

        // 3. Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $admin->syncRoles([$adminRole]);

        // 4. Create General Responden User (Memiliki beberapa role sekaligus)
        $responden1 = User::firstOrCreate(
            ['email' => 'responden@test.com'],
            [
                'name' => 'Responden User (Produsen & Reparatur)',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $responden1->syncRoles([
            'Responden',
            'Responden - Produsen UTTP',
            'Responden - Reparatur UTTP',
        ]);

        // 5. Create Responden User 2 (Masyarakat Umum)
        $responden2 = User::firstOrCreate(
            ['email' => 'responden2@test.com'],
            [
                'name' => 'Responden User (Masyarakat)',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $responden2->syncRoles([
            'Responden',
            'Responden - Masyarakat Umum',
        ]);
    }
}
