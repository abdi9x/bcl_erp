<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\room_category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Abdi Pranoto',
            'email' => 'Abdi.p43@gmail.com',
            'phone' => '082226955911',
            'img' => 'default.jpg',
            'password' => Hash::make('ratakgagas'),
        ]);
        $perms = [
            ['name' => 'Tambah Kamar', 'guard_name' => 'web'],
            ['name' => 'Edit Kamar', 'guard_name' => 'web'],
            ['name' => 'Hapus Kamar', 'guard_name' => 'web'],
            ['name' => 'Tambah Penyewa', 'guard_name' => 'web'],
            ['name' => 'Edit Penyewa', 'guard_name' => 'web'],
            ['name' => 'Hapus Penyewa', 'guard_name' => 'web'],
            ['name' => 'Tambah Inventaris', 'guard_name' => 'web'],
            ['name' => 'Edit Inventaris', 'guard_name' => 'web'],
            ['name' => 'Hapus  Inventaris', 'guard_name' => 'web'],
            ['name' => 'Perbaikan Inventaris', 'guard_name' => 'web'],
            ['name' => 'Edit Pricelist', 'guard_name' => 'web'],
            ['name' => 'Tambah Pricelist', 'guard_name' => 'web'],
            ['name' => 'Pemasukan', 'guard_name' => 'web'],
            ['name' => 'Pengeluaran', 'guard_name' => 'web'],
            ['name' => 'User Management', 'guard_name' => 'web'],
            ['name' => 'Hapus Pricelist', 'guard_name' => 'web']
        ];
        $role = Role::create([
            'name' => 'Administrator',
        ]);
        $permission = Permission::insert($perms);
        foreach ($perms as $perm) {
            $role->givePermissionTo($perm['name']);
        }
        // $role->givePermissionTo($permission);
        // $role->syncPermissions($permissions);
        $user = User::find(1);
        $user->assignRole('Administrator');

        $rooms_category = [
            ['category_name' => 'Sunset View Deluxe Room'],
            ['category_name' => 'Superior Room'],
            ['category_name' => 'Standard Room'],
        ];
        room_category::insert($rooms_category);
    }
}
