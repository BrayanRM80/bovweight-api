<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Persona extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $table      = 'personas';
    protected $primaryKey = 'cedula';
    protected $keyType    = 'string';
    public $incrementing  = false;

    const DELETED_AT = 'borrado_logico_en';

    protected $fillable = [
        'cedula', 'nombre', 'apellidos', 'correo',
        'contrasena', 'contacto', 'id_rol', 'activo',
    ];

    protected $hidden = ['contrasena', 'remember_token'];

    protected $casts = [
        'correo_verificado_en' => 'datetime',
        'activo'               => 'boolean',
    ];

    /** Laravel busca por defecto la columna "password"; redirigimos a "contrasena". */
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    /** Laravel busca "email" por defecto; redirigimos a "correo". */
    public function getEmailForVerification()
    {
        return $this->correo;
    }

    public function username()
    {
        return 'correo';
    }

    // ── Relaciones ──────────────────────────────────────────────────────────

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    /** Fincas asignadas (incluye las que es dueña) vía pivote */
    public function fincas()
    {
        return $this->belongsToMany(Finca::class, 'finca_persona', 'cedula', 'id_finca')
                    ->withPivot('es_dueno')
                    ->withTimestamps();
    }

    /** Solo las fincas donde es dueño */
    public function fincasComoDueno()
    {
        return $this->belongsToMany(Finca::class, 'finca_persona', 'cedula', 'id_finca')
                    ->wherePivot('es_dueno', true)
                    ->withPivot('es_dueno')
                    ->withTimestamps();
    }

    /** Pesajes registrados por esta persona */
    public function historialesAsignados()
    {
        return $this->hasMany(HistorialAnimal::class, 'cedula_asignador', 'cedula');
    }

    /** Recetas emitidas (si es veterinario) */
    public function recetasEmitidas()
    {
        return $this->hasMany(Receta::class, 'cedula_veterinario', 'cedula');
    }

    // ── Helpers de rol ──────────────────────────────────────────────────────

    public function tieneRol(string $nombreRol): bool
    {
        return $this->rol?->nombre === $nombreRol;
    }

    public function esAdministrador(): bool
{
    return $this->rol?->nombre === 'Administrador';
}

public function esGanadero(): bool
{
    return $this->rol?->nombre === 'Ganadero';
}

public function esAsistente(): bool
{
    return $this->rol?->nombre === 'Asistente';
}

public function esVeterinario(): bool
{
    return $this->rol?->nombre === 'Veterinario';
}

    /** IDs de fincas accesibles */
    public function fincasAccesiblesIds(): array
    {
        if ($this->esAdministrador()) {
            return Finca::pluck('id_finca')->toArray();
        }
        return $this->fincas()->pluck('fincas.id_finca')->toArray();
    }
}