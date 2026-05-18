<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class FincaPersona extends Pivot
{
    protected $table = 'finca_persona';
    public $timestamps = true;

    protected $fillable = ['id_finca', 'cedula', 'es_dueno'];

    protected $casts = [
        'es_dueno' => 'boolean',
    ];
}