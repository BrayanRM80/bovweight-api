<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PersonaResource;
use App\Models\Persona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** POST /api/v1/auth/login */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'correo'     => 'required|email',
            'contrasena' => 'required|string',
        ]);

        $persona = Persona::where('correo', $data['correo'])->first();

        if (! $persona || ! Hash::check($data['contrasena'], $persona->contrasena)) {
            throw ValidationException::withMessages([
                'correo' => ['Las credenciales no son correctas.'],
            ]);
        }

        if (! $persona->activo) {
            throw ValidationException::withMessages([
                'correo' => ['Esta cuenta está desactivada. Contacte al administrador.'],
            ]);
        }

        // Revocar tokens anteriores
        $persona->tokens()->delete();

        $token = $persona->createToken('app-token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'persona' => new PersonaResource($persona->load('rol')),
            'token'   => $token,
        ]);
    }

    /** POST /api/v1/auth/logout */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    /** GET /api/v1/auth/me */
    public function me(Request $request): JsonResponse
    {
        return response()->json(new PersonaResource($request->user()->load('rol')));
    }
}