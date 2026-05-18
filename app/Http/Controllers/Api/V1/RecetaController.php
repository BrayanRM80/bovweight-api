<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RecetaResource;
use App\Models\Animal;
use App\Models\Finca;
use App\Models\Receta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecetaController extends Controller
{
    /** Listar recetas de un animal */
    public function index(Request $request, Finca $finca, Animal $animal): JsonResponse
    {
        $this->authorize('view', $finca);

        $recetas = $animal->recetas()
            ->with(['medicamento', 'veterinario'])
            ->orderBy('fecha_emision', 'desc')
            ->get();

        return response()->json(RecetaResource::collection($recetas));
    }

    /** Crear receta — solo veterinarios */
    public function store(Request $request, Finca $finca, Animal $animal): JsonResponse
    {
        $persona = $request->user();

        if (! $persona->esVeterinario()) {
            return response()->json([
                'error' => 'Solo los veterinarios pueden emitir recetas.',
            ], 403);
        }

        // El veterinario debe tener acceso a la finca
        if (! in_array($finca->id_finca, $persona->fincasAccesiblesIds())) {
            return response()->json([
                'error' => 'No tienes acceso a esta finca.',
            ], 403);
        }

        $data = $request->validate([
            'id_medicamento' => 'required|exists:medicamentos,id_medicamento',
            'dosis'          => 'required|string|max:100',
            'frecuencia'     => 'required|string|max:100',
            'duracion_dias'  => 'required|integer|min:1|max:365',
            'fecha_emision'  => 'nullable|date',
            'diagnostico'    => 'nullable|string',
            'notas'          => 'nullable|string',
        ]);

        $receta = Receta::create(array_merge($data, [
            'numero_arete'       => $animal->numero_arete,
            'cedula_veterinario' => $persona->cedula,
            'fecha_emision'      => $data['fecha_emision'] ?? now()->toDateString(),
        ]));

        return response()->json(
            new RecetaResource($receta->load(['medicamento', 'veterinario'])),
            201
        );
    }

    public function show(Request $request, Finca $finca, Animal $animal, Receta $receta): JsonResponse
    {
        $this->authorize('view', $finca);
        return response()->json(new RecetaResource($receta->load(['medicamento', 'veterinario'])));
    }

    public function destroy(Request $request, Finca $finca, Animal $animal, Receta $receta): JsonResponse
    {
        $persona = $request->user();

        // Solo el veterinario que la emitió o el admin pueden borrarla
        if (! $persona->esAdministrador() && $receta->cedula_veterinario !== $persona->cedula) {
            return response()->json([
                'error' => 'Solo puedes eliminar recetas que tú emitiste.',
            ], 403);
        }

        $receta->delete();
        return response()->json(['message' => 'Receta eliminada correctamente.']);
    }
}