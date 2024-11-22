<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Responsible;
use App\Models\Room;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Rafael',
            'email' => 'r4faelmf@gmail.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'is_admin' => true,
            'slug' => 'rafael'
        ]);

        Room::factory()->create([
            'name' => 'Auditório A',
            'color' => '#FF0000',
            'description' => 'Auditório localizado no andar térreo do prédio administrativo do CCHLA',
            'capacity' => 30,
            'user_id' => 1,
            'resources' => json_encode(['Computador', 'Projetor', 'Webcam', 'Som'], JSON_HEX_TAG)
        ]);
        Room::factory()->create([
            'name' => 'Auditório B',
            'color' => '#0000FF',
            'description' => 'Auditório localizado no andar térreo do prédio administrativo do CCHLA',
            'capacity' => 80,
            'user_id' => 1,
            'resources' => json_encode([
                'Webcam',
                'Computador',
                'Projetor',
                'Som'
            ])
        ]);

        Responsible::factory()->create([
            'name' => 'Rafael',
            'phone' => '84998196201',
            'email' => 'r4faelmf@gmail.com',
            'matriculation' => '1952385',
            'category' => 'tecnico',
            'sector' => 'CCHLA'
        ]);
    }
}
