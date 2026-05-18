<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\EstadoAnimal;
use App\Models\Finca;
use Illuminate\Database\Seeder;

class AnimalSeeder extends Seeder
{
    public function run(): void
    {
        $finca   = Finca::where('nombre', 'Finca La Esperanza')->first();
        $estadoBien = EstadoAnimal::where('nombre_estado', 'Bien')->first();

        $animales = [
            [
                'numero_arete'     => 'CR-001-2024',
                'nombre'           => 'Lucero',
                'sexo'             => 'hembra',
                'raza'             => 'Brahman',
                'fecha_nacimiento' => '2022-03-15',
                'nota_general'     => 'Animal saludable, buen estado general.',
            ],
            [
                'numero_arete'     => 'CR-002-2024',
                'nombre'           => 'Tornado',
                'sexo'             => 'macho',
                'raza'             => 'Charolais',
                'fecha_nacimiento' => '2021-07-20',
                'nota_general'     => 'Toro reproductor.',
            ],
            [
                'numero_arete'     => 'CR-003-2024',
                'nombre'           => 'Estrella',
                'sexo'             => 'hembra',
                'raza'             => 'Holstein',
                'fecha_nacimiento' => '2023-01-10',
                'nota_general'     => 'En periodo de adaptación.',
            ],
        ];

        foreach ($animales as $data) {
            Animal::firstOrCreate(
                ['numero_arete' => $data['numero_arete']],
                array_merge($data, [
                    'id_finca'  => $finca->id_finca,
                    'id_estado' => $estadoBien->id_estado,
                    'activo'    => true,
                ])
            );
        }
    }
}