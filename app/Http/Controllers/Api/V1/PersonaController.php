<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FincaResource;
use App\Http\Resources\PersonaResource;
use App\Models\Finca;
use App\Models\Persona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PersonaController extends Controller
{
    /** Verifica que el usuario actual pueda gestionar personas */
    private function gateGestion(Request $request): bool
    {
        $persona = $request->user();
        return $persona && ($persona->esAdministrador() || $persona->esGanadero());
    }

    public function index(Request $request): JsonResponse
    {
        if (! $this->gateGestion($request)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $personas = Persona::with('rol')
            ->withCount('fincas')
            ->orderBy('nombre')
            ->get();

        return response()->json(PersonaResource::collection($personas));
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->gateGestion($request)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $data = $request->validate([
            'cedula'                  => 'required|string|max:20|unique:personas,cedula',
            'nombre'                  => 'required|string|max:100',
            'apellidos'               => 'required|string|max:100',
            'correo'                  => 'required|email|max:150|unique:personas,correo',
            'contrasena'              => 'required|string|min:8|confirmed',
            'contacto'                => 'nullable|string|max:50',
            'id_rol'                  => 'required|exists:roles,id_rol',
        ]);

        try {
            $persona = Persona::create([
                'cedula'     => $data['cedula'],
                'nombre'     => $data['nombre'],
                'apellidos'  => $data['apellidos'],
                'correo'     => $data['correo'],
                'contrasena' => Hash::make($data['contrasena']),
                'contacto'   => $data['contacto'] ?? null,
                'id_rol'     => $data['id_rol'],
                'activo'     => true,
            ]);

            return response()->json(
                new PersonaResource($persona->load('rol')),
                201
            );
        } catch (\Throwable $e) {
            Log::error('[PersonaController@store] ' . $e->getMessage());
            return response()->json([
                'error'   => 'Error al crear el usuario.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, Persona $persona): JsonResponse
    {
        if (! $this->gateGestion($request)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        return response()->json(
            new PersonaResource($persona->load('rol')->loadCount('fincas'))
        );
    }

    public function destroy(Request $request, Persona $persona): JsonResponse
    {
        if (! $this->gateGestion($request)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        try {
            $persona->update(['activo' => false]);
            $persona->delete(); // soft delete
            return response()->json(['message' => 'Usuario desactivado.']);
        } catch (\Throwable $e) {
            return response()->json([
                'error'   => 'Error al desactivar el usuario.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /** GET /personas/{persona}/fincas — fincas asignadas a la persona */
    public function fincas(Request $request, Persona $persona): JsonResponse
    {
        if (! $this->gateGestion($request)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $fincas = $persona->fincas()->withPivot('es_dueno')->get();

        return response()->json(
            $fincas->map(function ($f) {
                return [
                    'id_finca'  => $f->id_finca,
                    'nombre'    => $f->nombre,
                    'ubicacion' => $f->ubicacion,
                    'es_dueno'  => (bool) $f->pivot->es_dueno,
                ];
            })
        );
    }

    /** PUT /personas/{persona}/fincas — sincronizar fincas */
    public function syncFincas(Request $request, Persona $persona): JsonResponse
    {
        if (! $this->gateGestion($request)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $request->validate([
            'fincas'              => 'array',
            'fincas.*.id_finca'   => 'required|integer|exists:fincas,id_finca',
            'fincas.*.es_dueno'   => 'sometimes|boolean',
        ]);

        try {
            $sync = [];
            foreach ($request->fincas as $f) {
                $sync[$f['id_finca']] = [
                    'es_dueno' => $f['es_dueno'] ?? false,
                ];
            }

            $persona->fincas()->sync($sync);

            return response()->json(['message' => 'Fincas asignadas correctamente.']);
        } catch (\Throwable $e) {
            Log::error('[PersonaController@syncFincas] ' . $e->getMessage());
            return response()->json([
                'error'   => 'Error al asignar fincas.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}