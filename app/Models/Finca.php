<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Finca extends Model
{
    use HasFactory, SoftDeletes;

    protected $table      = 'fincas';
    protected $primaryKey = 'id_finca';

    const DELETED_AT = 'borrado_logico_en';

    protected $fillable = [
        'nombre', 'ubicacion', 'hectareas', 'notas', 'activo',
    ];

    protected $casts = [
        'activo'    => 'boolean',
        'hectareas' => 'float',
    ];

    public function personas()
    {
        return $this->belongsToMany(Persona::class, 'finca_persona', 'id_finca', 'cedula')
                    ->withPivot('es_dueno')
                    ->withTimestamps();
    }

    public function dueno()
    {
        return $this->belongsToMany(Persona::class, 'finca_persona', 'id_finca', 'cedula')
                    ->wherePivot('es_dueno', true)
                    ->withPivot('es_dueno')
                    ->withTimestamps();
    }

    public function animales()
    {
        return $this->hasMany(Animal::class, 'id_finca', 'id_finca');
    }
}