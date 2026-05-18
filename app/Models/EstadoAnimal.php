<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoAnimal extends Model
{
    protected $table      = 'estados_animal';
    protected $primaryKey = 'id_estado';

    protected $fillable = ['nombre_estado'];

    public function animales()
    {
        return $this->hasMany(Animal::class, 'id_estado', 'id_estado');
    }
}