<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Yayasan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        User::create([
            'foundation_id' => null,
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => Hash::make('coba123'),
            'role' => 'superadmin',
            'phone_number' => null,
        ]);
    }
}
