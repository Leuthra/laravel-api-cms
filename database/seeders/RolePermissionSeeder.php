<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    
        $managePosts = Permission::firstOrCreate(['name' => 'manage-posts', 'guard_name' => 'sanctum']);
        $manageUsers = Permission::firstOrCreate(['name' => 'manage-users', 'guard_name' => 'sanctum']);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $admin->givePermissionTo([$managePosts, $manageUsers]);

        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'sanctum']);
        $editor->givePermissionTo($managePosts);

        $reader = Role::firstOrCreate(['name' => 'reader', 'guard_name' => 'sanctum']);
        $reader->givePermissionTo($managePosts);
    }
}
