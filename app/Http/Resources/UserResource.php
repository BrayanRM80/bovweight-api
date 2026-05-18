<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'email'       => $this->email,
            'role'        => $this->role,
            'phone'       => $this->phone,
            'is_active'   => (bool) $this->is_active,
            'farms_count' => $this->farms_count ?? 0,
        ];
    }
}