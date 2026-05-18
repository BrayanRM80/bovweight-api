<?php

namespace Database\Seeders;

use App\Models\EstadoAnimal;
use Illuminate\Database\Seeder;

class EstadoAnimalSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            'Bien',
            'Enfermo',
            'Medicado',
            'En tratamiento',
            'Muerto',
            'Vendido',
        ];

        foreach ($estados as $nombre) {
            EstadoAnimal::firstOrCreate(['nombre_estado' => $nombre]);
        }
    }
}