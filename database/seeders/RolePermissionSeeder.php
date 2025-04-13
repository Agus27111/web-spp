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
    public function run()
    {
        // Define permissions
        $permissions = [
            'view-dashboard',
            'manage-academic',
            'manage-students',
            'manage-payments',
            'manage-fee-types',
            'manage-discounts',
            'manage-foundations',
            'manage-users',
            'manage-expenses',
            'manage-roles',
            'manage-permissions',
        ];

        // Create permissions if they do not exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Superadmin role and assign all permissions
        $superadmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $superadmin->givePermissionTo(Permission::all());

        // Create Foundation role and assign specific permissions
        $foundation = Role::firstOrCreate(['name' => 'foundation', 'guard_name' => 'web']);
        $foundation->givePermissionTo([
            'manage-foundations',
            'manage-users',
            'manage-expenses',
        ]);

        // Create Operator role and assign specific permissions
        $operator = Role::firstOrCreate(['name' => 'operator', 'guard_name' => 'web']);
        $operator->givePermissionTo([
            'manage-academic',
            'manage-students',
            'manage-payments',
            'manage-fee-types',
            'manage-discounts',
        ]);

        // Create Parent role and assign specific permission
        $parent = Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
        $parent->givePermissionTo([
            'view-dashboard',
        ]);
    }
}
