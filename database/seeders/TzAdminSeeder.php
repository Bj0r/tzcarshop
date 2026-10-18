<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TzAdminSeeder extends Seeder
{
    /**
     * Seed the initial system users (Admin & Customer).
     */
    public function run(): void
    {
        // 1. Seed Administrator Account
        User::updateOrCreate(
            [
                'username' => 'tz.admin',
            ],
            [
                'name' => 'Administrator',
                'email' => 'admin@email.com',
                'password' => Hash::make('Admin123454'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // 2. Seed Customer Account
        User::updateOrCreate(
            [
                'username' => 'tz.customer',
            ],
            [
                'name' => 'John Customer',
                'email' => 'customer@email.com',
                'password' => Hash::make('Customer123454'),
                'role' => 'customer',
                'status' => 'active',
            ]
        );
    }
}
