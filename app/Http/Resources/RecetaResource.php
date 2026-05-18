<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecetaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id_receta'     => $this->id_receta,
            'numero_arete'  => $this->numero_arete,
            'dosis'         => $this->dosis,
            'frecuencia'    => $this->frecuencia,
            'duracion_dias' => $this->duracion_dias,
            'fecha_emision' => $this->fecha_emision?->toDateString(),
            'diagnostico'   => $this->diagnostico,
            'notas'         => $this->notas,
            'medicamento'   => $this->whenLoaded('medicamento', fn() => [
                'id_medicamento' => $this->medicamento->id_medicamento,
                'nombre'         => $this->medicamento->nombre,
                'descripcion'    => $this->medicamento->descripcion,
            ]),
            'veterinario'   => $this->whenLoaded('veterinario', fn() => [
                'cedula'           => $this->veterinario->cedula,
                'nombre_completo'  => trim($this->veterinario->nombre . ' ' . $this->veterinario->apellidos),
            ]),
            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}