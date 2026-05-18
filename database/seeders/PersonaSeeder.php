<?php

namespace Database\Seeders;

use App\Models\Persona;
use App\Models\Rol;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PersonaSeeder extends Seeder
{
    public function run(): void
    {
        $rolAdmin   = Rol::where('nombre', 'Administrador')->first();
        $rolGana    = Rol::where('nombre', 'Ganadero')->first();
        $rolAsist   = Rol::where('nombre', 'Asistente')->first();
        $rolVet     = Rol::where('nombre', 'Veterinario')->first();

        Persona::firstOrCreate(
            ['cedula' => '101010101'],
            [
                'nombre'     => 'Carlos',
                'apellidos'  => 'Administrador BovWeight',
                'correo'     => 'admin@bovweight.cr',
                'contrasena' => Hash::make('password'),
                'contacto'   => '8888-0001',
                'id_rol'     => $rolAdmin->id_rol,
                'activo'     => true,
            ]
        );

        Persona::firstOrCreate(
            ['cedula' => '202020202'],
            [
                'nombre'     => 'Iván',
                'apellidos'  => 'Chavarría Rodríguez',
                'correo'     => 'ivan@finca.cr',
                'contrasena' => Hash::make('password'),
                'contacto'   => '8888-0002',
                'id_rol'     => $rolGana->id_rol,
                'activo'     => true,
            ]
        );

        Persona::firstOrCreate(
            ['cedula' => '303030303'],
            [
                'nombre'     => 'María',
                'apellidos'  => 'Asistente Rodríguez',
                'correo'     => 'maria@finca.cr',
                'contrasena' => Hash::make('password'),
                'contacto'   => '8888-0003',
                'id_rol'     => $rolAsist->id_rol,
                'activo'     => true,
            ]
        );

        Persona::firstOrCreate(
            ['cedula' => '404040404'],
            [
                'nombre'     => 'Dr. Luis',
                'apellidos'  => 'Veterinario Solís',
                'correo'     => 'luis@veterinario.cr',
                'contrasena' => Hash::make('password'),
                'contacto'   => '8888-0004',
                'id_rol'     => $rolVet->id_rol,
                'activo'     => true,
            ]
        );
    }
}