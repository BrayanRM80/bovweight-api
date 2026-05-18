<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory;

    protected $table      = 'recetas';
    protected $primaryKey = 'id_receta';

    protected $fillable = [
        'numero_arete', 'id_medicamento', 'cedula_veterinario',
        'dosis', 'frecuencia', 'duracion_dias', 'fecha_emision',
        'diagnostico', 'notas',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'duracion_dias' => 'integer',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class, 'numero_arete', 'numero_arete');
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'id_medicamento', 'id_medicamento');
    }

    public function veterinario()
    {
        return $this->belongsTo(Persona::class, 'cedula_veterinario', 'cedula');
    }
}