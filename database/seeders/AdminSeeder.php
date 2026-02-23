<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the admin user.
     */
    public function run(): void
    {
        // Create or update admin user (idempotent - safe to run multiple times)
        User::updateOrCreate(
            ['email' => 'admin@u-connect.com'],
            [
                'name' => 'Admin U-Connect',
                'code' => 'ADMIN001',
                'password' => Hash::make('Admin@2026'),
                'type' => 'admin',
                'phone_number' => '+223 X XXX XXXX',
                'description' => 'Administrateur du système U-Connect',
            ]
        );
    }
}
