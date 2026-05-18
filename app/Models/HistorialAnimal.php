<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialAnimal extends Model
{
    use HasFactory;

    protected $table      = 'historial_animal';
    protected $primaryKey = 'id_historial';

    protected $fillable = [
        'numero_arete', 'peso', 'peso_real', 'cedula_asignador',
        'id_medicamento', 'confianza', 'caja_deteccion',
        'tamano', 'notas', 'foto', 'fecha_de_foto', 'metodo',
    ];

    protected $casts = [
        'peso'           => 'float',
        'peso_real'      => 'float',
        'confianza'      => 'float',
        'caja_deteccion' => 'array',
        'fecha_de_foto'  => 'datetime',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class, 'numero_arete', 'numero_arete');
    }

    public function asignador()
    {
        return $this->belongsTo(Persona::class, 'cedula_asignador', 'cedula');
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'id_medicamento', 'id_medicamento');
    }
}