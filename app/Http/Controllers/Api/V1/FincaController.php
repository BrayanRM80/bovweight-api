<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FincaResource;
use App\Models\Finca;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FincaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $persona = $request->user();

        if ($persona->esAdministrador()) {
            $fincas = Finca::withCount('animales')->orderBy('nombre')->get();
        } else {
            $idsAccesibles = $persona->fincasAccesiblesIds();
            $fincas = Finca::whereIn('id_finca', $idsAccesibles)
                ->withCount('animales')
                ->orderBy('nombre')
                ->get();
        }

        return response()->json(FincaResource::collection($fincas));
    }

    public function store(Request $request): JsonResponse
    {
        $persona = $request->user();

        // Solo admin, ganadero o asistente pueden crear fincas
        if (! ($persona->esAdministrador() || $persona->esGanadero() || $persona->esAsistente())) {
            return response()->json(['error' => 'No tienes permiso para crear fincas.'], 403);
        }

        $data = $request->validate([
            'nombre'    => 'required|string|max:100',
            'ubicacion' => 'nullable|string|max:150',
            'hectareas' => 'nullable|numeric|min:0',
            'notas'     => 'nullable|string',
        ]);

        $finca = Finca::create($data + ['activo' => true]);

        // El creador queda como dueño (a menos que sea admin)
        if (! $persona->esAdministrador()) {
            $finca->personas()->attach($persona->cedula, ['es_dueno' => true]);
        }

        return response()->json(new FincaResource($finca->loadCount('animales')), 201);
    }

    public function show(Request $request, Finca $finca): JsonResponse
    {
        $this->authorize('view', $finca);
        return response()->json(new FincaResource($finca->loadCount('animales')));
    }

    public function update(Request $request, Finca $finca): JsonResponse
    {
        $this->authorize('update', $finca);

        $data = $request->validate([
            'nombre'    => 'sometimes|string|max:100',
            'ubicacion' => 'nullable|string|max:150',
            'hectareas' => 'nullable|numeric|min:0',
            'notas'     => 'nullable|string',
            'activo'    => 'sometimes|boolean',
        ]);

        $finca->update($data);
        return response()->json(new FincaResource($finca->fresh()));
    }

    public function destroy(Request $request, Finca $finca): JsonResponse
    {
        $this->authorize('delete', $finca);
        $finca->delete();
        return response()->json(['message' => 'Finca eliminada correctamente.']);
    }
}