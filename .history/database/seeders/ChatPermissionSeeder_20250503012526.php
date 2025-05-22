<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // Fixed import
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ChatPermissionSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles with specific guards
        Role::create(['name' => 'teacher', 'guard_name' => 'teacher'])
            ->givePermissionTo(['create-chat', 'delete-chat']);

        Role::create(['name' => 'admin', 'guard_name' => 'teacher'])
            ->givePermissionTo(['manage-users', 'delete-any-chat']);

        // Student role (if needed)
        Role::create(['name' => 'student', 'guard_name' => 'student'])
            ->givePermissionTo('view-chat');
    }
}
