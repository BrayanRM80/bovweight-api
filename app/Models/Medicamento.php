<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table      = 'medicamentos';
    protected $primaryKey = 'id_medicamento';

    const DELETED_AT = 'borrado_logico_en';

    protected $fillable = ['nombre', 'descripcion'];

    public function recetas()
    {
        return $this->hasMany(Receta::class, 'id_medicamento', 'id_medicamento');
    }
}