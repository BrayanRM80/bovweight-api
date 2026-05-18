<?php

namespace App\Http\Controllers\Api\V1;

use App\Exports\FincaReporteExport;
use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Finca;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReporteController extends Controller
{
    /** Reporte detallado de una finca (JSON) */
    public function resumenFinca(Request $request, Finca $finca): JsonResponse
    {
        $this->authorize('view', $finca);

        $animales = $finca->animales()
            ->with(['estado', 'ultimoHistorial'])
            ->orderBy('numero_arete')
            ->get();

        return response()->json([
            'finca' => [
                'id_finca'  => $finca->id_finca,
                'nombre'    => $finca->nombre,
                'ubicacion' => $finca->ubicacion,
                'hectareas' => $finca->hectareas,
                'notas'     => $finca->notas,
            ],
            'total_animales' => $animales->count(),
            'animales'       => $animales->map(fn($a) => [
                'numero_arete' => $a->numero_arete,
                'nombre'       => $a->nombre,
                'sexo'         => $a->sexo,
                'raza'         => $a->raza,
                'estado'       => $a->estado?->nombre_estado,
                'ultimo_peso'  => $a->ultimoHistorial
                    ? (float) ($a->ultimoHistorial->peso_real ?? $a->ultimoHistorial->peso)
                    : null,
                'ultima_fecha' => $a->ultimoHistorial?->created_at?->toDateString(),
            ]),
        ]);
    }

    /** Historial detallado de un animal */
    public function historialAnimal(Request $request, Finca $finca, Animal $animal): JsonResponse
    {
        $this->authorize('view', $finca);

        $historial = $animal->historiales()->orderBy('created_at')->get();

        $first = $historial->first();
        $last  = $historial->last();
        $gananciaTotal = ($first && $last && $first !== $last)
            ? round(($last->peso_real ?? $last->peso) - ($first->peso_real ?? $first->peso), 2)
            : null;

        return response()->json([
            'animal' => [
                'numero_arete' => $animal->numero_arete,
                'nombre'       => $animal->nombre,
            ],
            'ganancia_total' => $gananciaTotal,
            'historial'      => $historial->map(fn($h) => [
                'id'      => $h->id_historial,
                'fecha'   => $h->created_at?->toDateString(),
                'peso_kg' => (float) ($h->peso_real ?? $h->peso),
                'metodo'  => $h->metodo,
                'ganancia_kg' => null,
            ]),
        ]);
    }

    /** Exportar reporte de finca a Excel */
    public function exportExcel(Request $request, Finca $finca): BinaryFileResponse
    {
        $this->authorize('view', $finca);

        $nombre = preg_replace('/[^A-Za-z0-9_-]/', '_', $finca->nombre);
        $filename = "Reporte_{$nombre}_" . date('Ymd') . '.xlsx';

        return Excel::download(new FincaReporteExport($finca), $filename);
    }

    /** Exportar reporte de finca a PDF */
    public function exportPdf(Request $request, Finca $finca)
    {
        $this->authorize('view', $finca);

        $animales = $finca->animales()
            ->with(['estado', 'ultimoHistorial'])
            ->orderBy('numero_arete')
            ->get();

        $pdf = Pdf::loadView('reportes.finca', [
            'finca'    => $finca,
            'animales' => $animales,
        ])->setPaper('letter', 'portrait');

        $nombre = preg_replace('/[^A-Za-z0-9_-]/', '_', $finca->nombre);
        $filename = "Reporte_{$nombre}_" . date('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}