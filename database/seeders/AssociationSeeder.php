<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AssociationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample associations
        $associations = [
            [
                'name' => 'Croix Rouge',
                'email' => 'contact@croixrouge.org',
                'code' => 'CROIX_ROUGE_001',
                'password' => Hash::make('password123'),
                'type' => 'association',
                'phone_number' => '+33612345678',
                'description' => 'Organisation humanitaire internationale'
            ],
            [
                'name' => 'Médecins Sans Frontières',
                'email' => 'contact@msf.org',
                'code' => 'MSF_001',
                'password' => Hash::make('password123'),
                'type' => 'association',
                'phone_number' => '+33712345679',
                'description' => 'Aide médicale d\'urgence'
            ],
            [
                'name' => 'Unicef',
                'email' => 'contact@unicef.org',
                'code' => 'UNICEF_001',
                'password' => Hash::make('password123'),
                'type' => 'association',
                'phone_number' => '+33812345680',
                'description' => 'Protection de l\'enfance'
            ],
            [
                'name' => 'Greenpeace',
                'email' => 'contact@greenpeace.org',
                'code' => 'GREENPEACE_001',
                'password' => Hash::make('password123'),
                'type' => 'association',
                'phone_number' => '+33912345681',
                'description' => 'Protection de l\'environnement'
            ],
            [
                'name' => 'Oxfam',
                'email' => 'contact@oxfam.org',
                'code' => 'OXFAM_001',
                'password' => Hash::make('password123'),
                'type' => 'association',
                'phone_number' => '+33102345682',
                'description' => 'Lutte contre la pauvreté'
            ],
        ];

        foreach ($associations as $association) {
            User::firstOrCreate(
                ['code' => $association['code']],
                $association
            );
        }
    }
}

