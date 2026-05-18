<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PersonaResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(new PersonaResource($request->user()->load('rol')));
    }

    public function update(Request $request): JsonResponse
    {
        $persona = $request->user();

        $data = $request->validate([
            'nombre'             => 'sometimes|string|max:100',
            'apellidos'          => 'sometimes|string|max:150',
            'correo'             => 'sometimes|email|unique:personas,correo,' . $persona->cedula . ',cedula',
            'contacto'           => 'nullable|string|max:30',
            'contrasena_actual'  => 'required_with:contrasena|string',
            'contrasena'         => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($data['contrasena'])) {
            if (! Hash::check($data['contrasena_actual'] ?? '', $persona->contrasena)) {
                return response()->json([
                    'message' => 'La contraseña actual es incorrecta.',
                    'errors'  => ['contrasena_actual' => ['La contraseña actual es incorrecta.']],
                ], 422);
            }
            $persona->contrasena = Hash::make($data['contrasena']);
        }

        if (isset($data['nombre']))    $persona->nombre = $data['nombre'];
        if (isset($data['apellidos'])) $persona->apellidos = $data['apellidos'];
        if (isset($data['correo']))    $persona->correo = $data['correo'];
        if (array_key_exists('contacto', $data)) $persona->contacto = $data['contacto'];

        $persona->save();

        return response()->json([
            'message' => 'Perfil actualizado correctamente.',
            'persona' => new PersonaResource($persona->fresh()->load('rol')),
        ]);
    }
}