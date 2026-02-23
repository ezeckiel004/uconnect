<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ForumPost;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create association user
        $user = User::where('type', 'association')->first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'Espoir & Savoirs',
                'email' => 'espoir@test.com',
                'password' => bcrypt('password'),
                'type' => 'association',
                'code' => 'ESK001',
                'phone_number' => '1234567890',
                'description' => 'Association d\'aide à l\'éducation',
                'first_login' => false,
            ]);
        }

        // Get or create some member users for likes
        $member1 = User::where('email', 'member1@test.com')->first();
        if (!$member1) {
            $member1 = User::create([
                'name' => 'Jean Dupont',
                'email' => 'member1@test.com',
                'password' => bcrypt('password'),
                'type' => 'donor',
                'phone_number' => '1234567891',
                'description' => 'Bénévole actif',
                'first_login' => false,
            ]);
        }

        $member2 = User::where('email', 'member2@test.com')->first();
        if (!$member2) {
            $member2 = User::create([
                'name' => 'Marie Martin',
                'email' => 'member2@test.com',
                'password' => bcrypt('password'),
                'type' => 'donor',
                'phone_number' => '1234567892',
                'description' => 'Participante du projet',
                'first_login' => false,
            ]);
        }

        // Create test posts
        $post1 = ForumPost::create([
            'user_id' => $user->id,
            'title' => 'Créer un kit "urgence scolaire" à faible coût',
            'description' => 'On a réfléchi à un petit sac de base avec fournitures essentielles (ardoise, stylos, trousse). Avez-vous des fournisseurs à recommander ou des idées d\'amélioration ?',
            'category' => 'Idées',
            'likes' => 5,
            'views' => 23,
        ]);

        $post2 = ForumPost::create([
            'user_id' => $user->id,
            'title' => 'Modèle de convention de partenariat',
            'description' => 'Voici un modèle que nous utilisons pour officialiser nos collaborations avec d\'autres structures. Il est modifiable et simple à adapter à tout type de projet.',
            'category' => 'Ressources',
            'likes' => 12,
            'views' => 45,
            'file_name' => 'convention.pdf',
            'file_size' => '23 kb',
        ]);

        $post3 = ForumPost::create([
            'user_id' => $user->id,
            'title' => 'Conseils pour la logistique',
            'description' => 'Besoin d\'aide pour optimiser la logistique de notre prochaine distribution. Quels outils utilisez-vous ?',
            'category' => 'Logistique',
            'likes' => 3,
            'views' => 12,
        ]);

        // Add likes from members
        $post1->likedByUsers()->attach([$member1->id, $member2->id]);
        $post2->likedByUsers()->attach([$member1->id]);
        $post3->likedByUsers()->attach([$member2->id]);

        echo "Forum test data seeded!\n";
    }
}

