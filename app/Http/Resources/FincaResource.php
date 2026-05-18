<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FincaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id_finca'      => $this->id_finca,
            'nombre'        => $this->nombre,
            'ubicacion'     => $this->ubicacion,
            'hectareas'     => $this->hectareas,
            'notas'         => $this->notas,
            'activo'        => (bool) $this->activo,
            'animales_count' => $this->animales_count ?? 0,
            'es_dueno'      => $this->whenPivotLoaded('finca_persona', fn() => (bool) $this->pivot->es_dueno),
            'created_at'    => $this->created_at?->toDateString(),
        ];
    }
}