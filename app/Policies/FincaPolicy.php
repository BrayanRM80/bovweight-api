<?php

namespace App\Policies;

use App\Models\Finca;
use App\Models\Persona;

class FincaPolicy
{
    /** Cualquier usuario autenticado puede ver la lista de fincas que le corresponden */
    public function viewAny(Persona $persona): bool
    {
        return true;
    }

    /** Ver una finca específica */
    public function view(Persona $persona, Finca $finca): bool
    {
        // Admin ve todo
        if ($persona->esAdministrador()) {
            return true;
        }

        // Otros roles: deben estar asignados a la finca
        return $finca->personas()
            ->where('finca_persona.cedula', $persona->cedula)
            ->exists();
    }

    /** Crear nueva finca: admin, ganadero o asistente */
    public function create(Persona $persona): bool
    {
        return $persona->esAdministrador()
            || $persona->esGanadero()
            || $persona->esAsistente();
    }

    /** Editar finca: admin, o usuarios asignados (excepto veterinarios) */
    public function update(Persona $persona, Finca $finca): bool
    {
        if ($persona->esAdministrador()) {
            return true;
        }

        // Veterinarios no pueden editar fincas
        if ($persona->esVeterinario()) {
            return false;
        }

        // Ganadero o Asistente: deben estar asignados
        return $finca->personas()
            ->where('finca_persona.cedula', $persona->cedula)
            ->exists();
    }

    /** Eliminar finca: admin o ganadero dueño */
    public function delete(Persona $persona, Finca $finca): bool
    {
        if ($persona->esAdministrador()) {
            return true;
        }

        // Ganadero solo puede borrar fincas donde sea dueño
        if ($persona->esGanadero()) {
            return $finca->personas()
                ->where('finca_persona.cedula', $persona->cedula)
                ->where('finca_persona.es_dueno', true)
                ->exists();
        }

        return false;
    }

    /** Restaurar finca eliminada */
    public function restore(Persona $persona, Finca $finca): bool
    {
        return $persona->esAdministrador();
    }

    /** Eliminar permanentemente */
    public function forceDelete(Persona $persona, Finca $finca): bool
    {
        return $persona->esAdministrador();
    }
}