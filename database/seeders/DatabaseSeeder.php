<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create or update test user (idempotent - safe to run multiple times)
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'type' => 'donor',
            ]
        );

        // Call admin seeder
        $this->call(AdminSeeder::class);

        // Call association seeder
        $this->call(AssociationSeeder::class);
    }
}
