<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PerfilController;
use App\Http\Controllers\Api\V1\PersonaController;
use App\Http\Controllers\Api\V1\FincaController;
use App\Http\Controllers\Api\V1\AnimalController;
use App\Http\Controllers\Api\V1\HistorialController;
use App\Http\Controllers\Api\V1\RecetaController;
use App\Http\Controllers\Api\V1\MedicamentoController;
use App\Http\Controllers\Api\V1\EstadoAnimalController;
use App\Http\Controllers\Api\V1\RolController;
use App\Http\Controllers\Api\V1\ReporteController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ── Login público ──────────────────────────────────────────────────────
    Route::post('auth/login', [AuthController::class, 'login']);

    // ── Rutas protegidas ───────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me',      [AuthController::class, 'me']);

        // Perfil propio
        Route::get('perfil', [PerfilController::class, 'show']);
        Route::put('perfil', [PerfilController::class, 'update']);

        // Catálogos (cualquier usuario autenticado)
        Route::get('roles',         [RolController::class, 'index']);
        Route::get('estados',       [EstadoAnimalController::class, 'index']);
        Route::get('medicamentos',  [MedicamentoController::class, 'index']);

        // Crear/eliminar medicamentos (admin o vet)
        Route::post('medicamentos',                [MedicamentoController::class, 'store']);
        Route::delete('medicamentos/{medicamento}', [MedicamentoController::class, 'destroy']);

        // Gestión de personas (solo admin)
       
            Route::get('personas',                          [PersonaController::class, 'index']);
            Route::post('personas',                         [PersonaController::class, 'store']);
            Route::get('personas/{persona}',                [PersonaController::class, 'show']);
            Route::delete('personas/{persona}',             [PersonaController::class, 'destroy']);
            Route::get('personas/{persona}/fincas',         [PersonaController::class, 'fincas']);
            Route::put('personas/{persona}/fincas',         [PersonaController::class, 'syncFincas']);
       

        // Fincas
        Route::apiResource('fincas', FincaController::class);

        // Animales (anidados bajo finca)
        Route::apiResource('fincas.animales', AnimalController::class)
            ->parameters(['animales' => 'animal']);

        // Cambiar estado del animal
        Route::patch('fincas/{finca}/animales/{animal}/estado',
            [AnimalController::class, 'cambiarEstado']);

        // Historial / pesajes
        Route::get('fincas/{finca}/animales/{animal}/historial',
            [HistorialController::class, 'index']);
        Route::post('fincas/{finca}/animales/{animal}/historial',
            [HistorialController::class, 'store']);
        Route::get('fincas/{finca}/animales/{animal}/historial/{historial}',
            [HistorialController::class, 'show']);
        Route::patch('fincas/{finca}/animales/{animal}/historial/{historial}/corregir',
            [HistorialController::class, 'corregir']);

        // Recetas (veterinario)
        Route::get('fincas/{finca}/animales/{animal}/recetas',
            [RecetaController::class, 'index']);
        Route::post('fincas/{finca}/animales/{animal}/recetas',
            [RecetaController::class, 'store']);
        Route::get('fincas/{finca}/animales/{animal}/recetas/{receta}',
            [RecetaController::class, 'show']);
        Route::delete('fincas/{finca}/animales/{animal}/recetas/{receta}',
            [RecetaController::class, 'destroy']);

        // Reportes
        Route::get('fincas/{finca}/reporte',                       [ReporteController::class, 'resumenFinca']);
        Route::get('/fincas/{finca}/reporte/excel', [ReporteController::class, 'exportExcel']);
Route::get('/fincas/{finca}/reporte/pdf',   [ReporteController::class, 'exportPdf']);
        Route::get('fincas/{finca}/animales/{animal}/historial-reporte',
            [ReporteController::class, 'historialAnimal']);
    });
});