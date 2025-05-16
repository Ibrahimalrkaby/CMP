<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChatPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Teacher Guard Permissions
        Permission::create(['name' => 'create-chat', 'guard_name' => 'teacher']);
        Permission::create(['name' => 'delete-chat', 'guard_name' => 'teacher']);

        // Student Guard Permissions
        Permission::create(['name' => 'view-chat', 'guard_name' => 'student']);

        // Teacher Roles
        $teacherAdmin = Role::create(['name' => 'admin', 'guard_name' => 'teacher'])
            ->givePermissionTo(['create-chat', 'delete-chat']);

        $teacher = Role::create(['name' => 'teacher', 'guard_name' => 'teacher'])
            ->givePermissionTo('create-chat');

        // Student Role
        Role::create(['name' => 'student', 'guard_name' => 'student'])
            ->givePermissionTo('view-chat');
    }
}
