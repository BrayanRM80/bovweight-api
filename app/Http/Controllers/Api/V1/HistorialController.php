<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\HistorialResource;
use App\Models\Animal;
use App\Models\Finca;
use App\Models\HistorialAnimal;
use App\Services\MlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistorialController extends Controller
{
    public function __construct(private MlService $mlService) {}

    public function index(Request $request, Finca $finca, Animal $animal): JsonResponse
    {
        $this->authorize('view', $finca);

        $historiales = $animal->historiales()
            ->with(['asignador', 'medicamento'])
            ->paginate(20);

        return response()->json($historiales);
    }

    public function store(Request $request, Finca $finca, Animal $animal): JsonResponse
    {
        $this->authorize('view', $finca);

        $request->validate([
            'foto'            => 'required_without:peso|image|mimes:jpg,jpeg,png|max:10240',
            'peso'            => 'required_without:foto|numeric|min:20|max:1200',
            'distance_meters' => 'nullable|numeric|min:0.5|max:20',
            'photo_angle'     => 'nullable|in:lateral,diagonal,frontal,posterior',
            'tamano'          => 'nullable|string|max:50',
            'notas'           => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $animal) {
            $historialData = [
                'numero_arete'      => $animal->numero_arete,
                'cedula_asignador'  => $request->user()->cedula,
                'tamano'            => $request->input('tamano'),
                'notas'             => $request->input('notas'),
                'fecha_de_foto'     => now(),
            ];

            if ($request->hasFile('foto')) {
                // Pesaje por foto: llamar al ML
                $resultado = $this->mlService->estimateWeight(
                    $request->file('foto'),
                    $animal->numero_arete,
                    $request->input('distance_meters'),
                    $request->input('photo_angle'),
                );

                if (! ($resultado['bovine_detected'] ?? false)) {
                    throw new \RuntimeException(
                        $resultado['warning'] ?? 'No se detectó ningún bovino en la imagen.'
                    );
                }

                $rutaFoto = $request->file('foto')->store(
                    "historial/{$animal->numero_arete}",
                    'public'
                );

                $historialData = array_merge($historialData, [
                    'peso'           => $resultado['estimated_weight_kg'],
                    'confianza'      => $resultado['confidence'],
                    'caja_deteccion' => $resultado['bounding_box'],
                    'foto'           => $rutaFoto,
                    'metodo'         => 'fotografia',
                ]);
            } else {
                // Pesaje manual
                $historialData = array_merge($historialData, [
                    'peso'      => $request->input('peso'),
                    'peso_real' => $request->input('peso'),
                    'metodo'    => 'manual',
                ]);
            }

            $historial = HistorialAnimal::create($historialData);

            return response()->json(new HistorialResource($historial->load('asignador')), 201);
        });
    }

    public function show(Request $request, Finca $finca, Animal $animal, HistorialAnimal $historial): JsonResponse
    {
        $this->authorize('view', $finca);
        return response()->json(new HistorialResource($historial->load(['asignador', 'medicamento'])));
    }

    /** PATCH corregir peso real */
    public function corregir(Request $request, Finca $finca, Animal $animal, HistorialAnimal $historial): JsonResponse
    {
        $this->authorize('view', $finca);

        $request->validate([
            'peso_real' => 'required|numeric|min:20|max:1200',
            'notas'     => 'nullable|string',
        ]);

        $historial->update([
            'peso_real' => $request->peso_real,
            'notas'     => $request->notas ?? $historial->notas,
        ]);

        // Notificar al ML para feedback
        try {
            $this->mlService->sendFeedback(
                $animal->numero_arete,
                (float) $historial->peso,
                (float) $request->peso_real,
                $request->notas,
            );
        } catch (\Throwable $e) {
            // Si el ML falla, no bloqueamos la corrección
        }

        return response()->json(new HistorialResource($historial->fresh()));
    }
}