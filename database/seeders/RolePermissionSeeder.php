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
        $permissions = [
            'create fondation',
            'view all fondations',
            'manage operators',
            'manage parents',
            'manage students',
            'manage classes',
            'manage fees',
            'manage fee types',
            'manage discounts',
            'manage payments',
            'manage incomes',
            'manage expenses',
            'view payment history',
            'view spp report',
            'print report',
            'view own bill',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Superadmin
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $superadmin->givePermissionTo(Permission::all());

        // Fondation
        $fondation = Role::firstOrCreate(['name' => 'fondation']);
        $fondation->givePermissionTo([
            'manage operators',
            'view payment history',
            'view spp report',
            'print report',
        ]);

        // Operator
        $operator = Role::firstOrCreate(['name' => 'operator']);
        $operator->givePermissionTo([
            'manage parents',
            'manage students',
            'manage classes',
            'manage fees',
            'manage fee types',
            'manage discounts',
            'manage payments',
            'manage incomes',
            'manage expenses',
            'view payment history',
            'view spp report',
            'print report',
        ]);

        // Parent
        $parent = Role::firstOrCreate(['name' => 'parent']);
        $parent->givePermissionTo(['view own bill']);
    }
}
