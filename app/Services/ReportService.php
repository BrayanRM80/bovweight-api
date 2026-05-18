<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\Farm;

class ReportService
{
    public function animalWeightHistory(Animal $animal): array
    {
        // Traer pesajes en orden cronológico ASCENDENTE (del más viejo al más nuevo)
        $weighings = $animal->weighings()
            ->orderBy('created_at', 'asc')
            ->get(['id', 'estimated_weight_kg', 'real_weight_kg', 'method', 'created_at']);

        // Construir array de pesos en orden cronológico
        $items = [];
        foreach ($weighings as $w) {
            $items[] = [
                'weighing_id' => $w->id,
                'date'        => $w->created_at ? $w->created_at->toDateString() : null,
                'weight_kg'   => (float) ($w->real_weight_kg ?? $w->estimated_weight_kg ?? 0),
                'method'      => $w->method,
            ];
        }

        // Calcular ganancia total (último - primero) en orden cronológico
        $totalGain = null;
        if (count($items) > 1) {
            $first = $items[0]['weight_kg'];
            $last  = $items[count($items) - 1]['weight_kg'];
            $totalGain = round($last - $first, 2);
        }

        // Invertir: ahora el más reciente queda primero
        $items = array_reverse($items);

        // Calcular gain_kg comparando cada pesaje con el SIGUIENTE (el más antiguo en la lista)
        // En orden descendente: items[0] = más reciente, items[1] = anterior, etc.
        // gain_kg de items[i] = items[i].weight - items[i+1].weight
        $history = [];
        foreach ($items as $i => $item) {
            $next = $items[$i + 1] ?? null;
            $gain = $next !== null
                ? round($item['weight_kg'] - $next['weight_kg'], 2)
                : null;

            $history[] = [
                'weighing_id' => $item['weighing_id'],
                'date'        => $item['date'],
                'weight_kg'   => $item['weight_kg'],
                'gain_kg'     => $gain,
                'method'      => $item['method'],
            ];
        }

        return [
            'animal_id'  => $animal->id,
            'animal_tag' => $animal->tag,
            'total_gain' => $totalGain,
            'history'    => $history,
        ];
    }

    public function farmSummary(Farm $farm): array
    {
        $animals = $farm->animals()->with('latestWeighing')->get();

        $summary = $animals->map(function ($animal) {
            $w = $animal->latestWeighing;
            return [
                'animal_id'    => $animal->id,
                'tag'          => $animal->tag,
                'name'         => $animal->name,
                'last_weight'  => $w ? (float) ($w->real_weight_kg ?? $w->estimated_weight_kg ?? 0) : null,
                'last_weighed' => $w && $w->created_at ? $w->created_at->toDateString() : null,
            ];
        })->values()->toArray();

        return [
            'farm_id'       => $farm->id,
            'farm_name'     => $farm->name,
            'total_animals' => $animals->count(),
            'animals'       => $summary,
        ];
    }
}