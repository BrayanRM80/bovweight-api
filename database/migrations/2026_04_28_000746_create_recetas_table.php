<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recetas', function (Blueprint $table) {
            $table->id('id_receta');

            $table->string('numero_arete', 50);
            $table->foreign('numero_arete')->references('numero_arete')->on('animales')->cascadeOnDelete();

            $table->unsignedBigInteger('id_medicamento');
            $table->foreign('id_medicamento')->references('id_medicamento')->on('medicamentos')->restrictOnDelete();

            // Cédula del veterinario que receta
            $table->string('cedula_veterinario', 20);
            $table->foreign('cedula_veterinario')->references('cedula')->on('personas')->restrictOnDelete();

            $table->string('dosis', 100);                    // ej. "5 ml", "1 tableta"
            $table->string('frecuencia', 100);               // ej. "cada 8 horas"
            $table->integer('duracion_dias');                // días totales del tratamiento
            $table->date('fecha_emision');
            $table->text('diagnostico')->nullable();         // diagnóstico del veterinario
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recetas');
    }
};