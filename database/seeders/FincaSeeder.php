<?php

namespace Database\Seeders;

use App\Models\Finca;
use App\Models\Persona;
use Illuminate\Database\Seeder;

class FincaSeeder extends Seeder
{
    public function run(): void
    {
        $ivan  = Persona::where('correo', 'ivan@finca.cr')->first();
        $maria = Persona::where('correo', 'maria@finca.cr')->first();
        $luis  = Persona::where('correo', 'luis@veterinario.cr')->first();

        $finca = Finca::firstOrCreate(
            ['nombre' => 'Finca La Esperanza'],
            [
                'ubicacion' => 'Tilarán, Guanacaste',
                'hectareas' => 25.50,
                'notas'     => 'Finca dedicada a ganado de engorde y producción lechera.',
                'activo'    => true,
            ]
        );

        // Iván es el dueño (es_dueno = true)
        $finca->personas()->syncWithoutDetaching([
            $ivan->cedula  => ['es_dueno' => true],
            $maria->cedula => ['es_dueno' => false],
            $luis->cedula  => ['es_dueno' => false],
        ]);

        $finca2 = Finca::firstOrCreate(
            ['nombre' => 'Finca Monte Verde'],
            [
                'ubicacion' => 'Nazareth, Guanacaste',
                'hectareas' => 12.00,
                'notas'     => 'Finca dedicada a ganado lechero.',
                'activo'    => true,
            ]
        );

        $finca2->personas()->syncWithoutDetaching([
            $ivan->cedula => ['es_dueno' => true],
        ]);
    }
}