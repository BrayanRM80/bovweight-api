<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'cedula'       => $this->cedula,
            'nombre'       => $this->nombre,
            'apellidos'    => $this->apellidos,
            'nombre_completo' => trim($this->nombre . ' ' . $this->apellidos),
            'correo'       => $this->correo,
            'contacto'     => $this->contacto,
            'activo'       => (bool) $this->activo,
            'rol'          => $this->whenLoaded('rol', fn() => [
                'id_rol' => $this->rol->id_rol,
                'nombre' => $this->rol->nombre,
            ]),
            'fincas_count' => $this->fincas_count ?? 0,
        ];
    }
}