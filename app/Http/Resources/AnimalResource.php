<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class AnimalResource extends JsonResource
{
    public function toArray($request): array
    {
        // Manejo seguro del último historial
        $ultimo = null;
        if ($this->relationLoaded('ultimoHistorial')) {
            $rel = $this->getRelation('ultimoHistorial');
            if ($rel && ! ($rel instanceof MissingValue)) {
                $ultimo = $rel;
            }
        }

        return [
            'numero_arete'     => $this->numero_arete,
            'nombre'           => $this->nombre,
            'id_finca'         => $this->id_finca,
            'sexo'             => $this->sexo,
            'raza'             => $this->raza,
            'fecha_nacimiento' => $this->fecha_nacimiento?->toDateString(),
            'nota_general'     => $this->nota_general,
            'activo'           => (bool) $this->activo,

            'estado' => $this->whenLoaded('estado', fn() => [
                'id_estado'     => $this->estado->id_estado,
                'nombre_estado' => $this->estado->nombre_estado,
            ]),

            'ultimo_peso'  => $ultimo ? (float) ($ultimo->peso_real ?? $ultimo->peso) : null,
            'ultima_fecha' => $ultimo?->created_at?->toDateString(),

            'historiales'  => HistorialResource::collection($this->whenLoaded('historiales')),
        ];
    }
}