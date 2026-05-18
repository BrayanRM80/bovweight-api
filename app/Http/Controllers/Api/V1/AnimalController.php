<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnimalResource;
use App\Models\Animal;
use App\Models\EstadoAnimal;
use App\Models\Finca;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnimalController extends Controller
{
    public function index(Request $request, Finca $finca): JsonResponse
    {
        $this->authorize('view', $finca);

        try {
            $animales = $finca->animales()
                ->with(['estado', 'ultimoHistorial'])
                ->orderBy('numero_arete')
                ->get();

            return response()->json(AnimalResource::collection($animales));
        } catch (\Throwable $e) {
            Log::error('[AnimalController@index] ' . $e->getMessage(), [
                'finca_id' => $finca->id_finca,
                'trace'    => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error'   => 'Error al cargar los animales.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request, Finca $finca): JsonResponse
    {
        $this->authorize('update', $finca);

        $data = $request->validate([
            'numero_arete'     => 'required|string|max:50|unique:animales,numero_arete',
            'nombre'           => 'nullable|string|max:100',
            'sexo'             => 'required|in:macho,hembra',
            'raza'             => 'required|string|max:50',
            'fecha_nacimiento' => 'nullable|date',
            'nota_general'     => 'nullable|string',
            'id_estado'        => 'nullable|exists:estados_animal,id_estado',
        ]);

        try {
            if (empty($data['id_estado'])) {
                $estadoBien = EstadoAnimal::where('nombre_estado', 'Bien')->first();
                if ($estadoBien) {
                    $data['id_estado'] = $estadoBien->id_estado;
                }
            }

            $animal = Animal::create($data + [
                'id_finca' => $finca->id_finca,
                'activo'   => true,
            ]);

            return response()->json(
                new AnimalResource($animal->load(['estado', 'finca'])),
                201
            );
        } catch (\Throwable $e) {
            Log::error('[AnimalController@store] ' . $e->getMessage(), [
                'data'  => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error'   => 'Error al crear el animal.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, Finca $finca, Animal $animal): JsonResponse
    {
        $this->authorize('view', $finca);

        try {
            $animal->load(['estado', 'finca', 'ultimoHistorial']);

            $historiales = $animal->historiales()->limit(5)->get();
            $animal->setRelation('historiales', $historiales);

            return response()->json(new AnimalResource($animal));
        } catch (\Throwable $e) {
            Log::error('[AnimalController@show] ' . $e->getMessage(), [
                'numero_arete' => $animal->numero_arete,
                'trace'        => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error'   => 'Error al cargar el animal.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, Finca $finca, Animal $animal): JsonResponse
    {
        $this->authorize('update', $finca);

        $data = $request->validate([
            'nombre'           => 'nullable|string|max:100',
            'sexo'             => 'sometimes|in:macho,hembra',
            'raza'             => 'sometimes|string|max:50',
            'fecha_nacimiento' => 'nullable|date',
            'nota_general'     => 'nullable|string',
            'id_estado'        => 'sometimes|exists:estados_animal,id_estado',
            'activo'           => 'sometimes|boolean',
        ]);

        try {
            $animal->update($data);
            return response()->json(
                new AnimalResource($animal->fresh()->load(['estado', 'finca']))
            );
        } catch (\Throwable $e) {
            Log::error('[AnimalController@update] ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error'   => 'Error al actualizar el animal.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /** DELETE — soft delete del animal */
    public function destroy(Request $request, Finca $finca, Animal $animal): JsonResponse
{
    $this->authorize('update', $finca);  // ← cambiado de 'delete' a 'update'

    try {
        DB::transaction(function () use ($animal) {
            $animal->delete();
        });

        return response()->json(['message' => 'Animal eliminado correctamente.']);
    } catch (\Throwable $e) {
        Log::error('[AnimalController@destroy] ' . $e->getMessage(), [
            'numero_arete' => $animal->numero_arete,
            'trace'        => $e->getTraceAsString(),
        ]);
        return response()->json([
            'error'   => 'Error al eliminar el animal.',
            'message' => $e->getMessage(),
        ], 500);
    }
}

    public function cambiarEstado(Request $request, Finca $finca, Animal $animal): JsonResponse
    {
        $this->authorize('view', $finca);

        $request->validate([
            'id_estado' => 'required|exists:estados_animal,id_estado',
        ]);

        try {
            $animal->update(['id_estado' => $request->id_estado]);

            return response()->json([
                'message' => 'Estado del animal actualizado.',
                'animal'  => new AnimalResource($animal->fresh()->load('estado')),
            ]);
        } catch (\Throwable $e) {
            Log::error('[AnimalController@cambiarEstado] ' . $e->getMessage());
            return response()->json([
                'error'   => 'Error al cambiar el estado.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}