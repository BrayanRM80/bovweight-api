<?php

namespace Database\Seeders;

use App\Models\Medicamento;
use Illuminate\Database\Seeder;

class MedicamentoSeeder extends Seeder
{
    public function run(): void
    {
        $medicamentos = [
            ['nombre' => 'Ivermectina',     'descripcion' => 'Antiparasitario inyectable de amplio espectro.'],
            ['nombre' => 'Oxitetraciclina', 'descripcion' => 'Antibiótico de amplio espectro.'],
            ['nombre' => 'Vitamina AD3E',   'descripcion' => 'Suplemento vitamínico para deficiencias.'],
            ['nombre' => 'Penicilina',      'descripcion' => 'Antibiótico para infecciones bacterianas.'],
            ['nombre' => 'Vacuna IBR',      'descripcion' => 'Vacuna contra Rinotraqueítis Infecciosa Bovina.'],
            ['nombre' => 'Desparasitante oral', 'descripcion' => 'Tratamiento contra parásitos internos.'],
        ];

        foreach ($medicamentos as $med) {
            Medicamento::firstOrCreate(['nombre' => $med['nombre']], $med);
        }
    }
}