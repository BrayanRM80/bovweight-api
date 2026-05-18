<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Animal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table      = 'animales';
    protected $primaryKey = 'numero_arete';
    protected $keyType    = 'string';
    public $incrementing  = false;

    const DELETED_AT = 'borrado_logico_en';

    protected $fillable = [
        'numero_arete', 'nombre', 'id_finca', 'sexo',
        'id_estado', 'raza', 'fecha_nacimiento', 'nota_general', 'activo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo'           => 'boolean',
    ];

    public function finca()
    {
        return $this->belongsTo(Finca::class, 'id_finca', 'id_finca');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoAnimal::class, 'id_estado', 'id_estado');
    }

    public function historiales()
    {
        return $this->hasMany(HistorialAnimal::class, 'numero_arete', 'numero_arete')
                    ->orderBy('created_at', 'desc');
    }

    /** Último pesaje — usando created_at explícitamente */
    public function ultimoHistorial()
    {
        return $this->hasOne(HistorialAnimal::class, 'numero_arete', 'numero_arete')
                    ->latest('created_at');
    }

    public function recetas()
    {
        return $this->hasMany(Receta::class, 'numero_arete', 'numero_arete')
                    ->orderBy('fecha_emision', 'desc');
    }
}