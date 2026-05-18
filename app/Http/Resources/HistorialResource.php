<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HistorialResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id_historial'   => $this->id_historial,
            'numero_arete'   => $this->numero_arete,
            'peso'           => (float) $this->peso,
            'peso_real'      => $this->peso_real ? (float) $this->peso_real : null,
            'confianza'      => $this->confianza ? (float) $this->confianza : null,
            'caja_deteccion' => $this->caja_deteccion,
            'tamano'         => $this->tamano,
            'notas'          => $this->notas,
            'foto'           => $this->foto,
            'fecha_de_foto'  => $this->fecha_de_foto?->toIso8601String(),
            'metodo'         => $this->metodo,
            'asignador'      => $this->whenLoaded('asignador', fn() => [
                'cedula' => $this->asignador->cedula,
                'nombre' => trim($this->asignador->nombre . ' ' . $this->asignador->apellidos),
            ]),
            'medicamento'    => $this->whenLoaded('medicamento', fn() => $this->medicamento),
            'created_at'     => $this->created_at?->toIso8601String(),
        ];
    }
}