<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // Fixed import
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ChatPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create global permissions (no guard_name)
        Permission::create(['name' => 'create-chat']);
        Permission::create(['name' => 'delete-chat']);
        Permission::create(['name' => 'view-chat']);

        // Teacher Guard Roles
        Role::create(['name' => 'admin', 'guard_name' => 'admin'])
            ->givePermissionTo(['create-chat', 'delete-chat', 'view-chat']);

        Role::create(['name' => 'teacher', 'guard_name' => 'teacher'])
            ->givePermissionTo(['create-chat', 'view-chat']);

        // Student Guard Role
        Role::create(['name' => 'student', 'guard_name' => 'student'])
            ->givePermissionTo('view-chat');
    }
}
