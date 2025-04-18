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
            'manage-incomes',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superadmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $superadmin->givePermissionTo(Permission::all());

        $foundation = Role::firstOrCreate(['name' => 'foundation', 'guard_name' => 'web']);
        $foundation->givePermissionTo([
            'manage-foundations',
            'manage-users',
            'manage-expenses',
        ]);

        $operator = Role::firstOrCreate(['name' => 'operator', 'guard_name' => 'web']);
        $operator->givePermissionTo([
            'manage-academic',
            'manage-students',
            'manage-payments',
            'manage-fee-types',
            'manage-discounts',
        ]);

        $parent = Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
        $parent->givePermissionTo([
            'view-dashboard',
            'manage-discounts',
        ]);
    }
}
