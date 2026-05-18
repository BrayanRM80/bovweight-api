<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Administrador', 'Ganadero', 'Asistente', 'Veterinario'];

        foreach ($roles as $nombre) {
            Rol::firstOrCreate(['nombre' => $nombre]);
        }
    }
}