<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('historial_animal', function (Blueprint $table) {
            $table->id('id_historial');

            $table->string('numero_arete', 50);
            $table->foreign('numero_arete')->references('numero_arete')->on('animales')->cascadeOnDelete();

            $table->decimal('peso', 7, 2);                    // peso estimado
            $table->decimal('peso_real', 7, 2)->nullable();   // peso corregido (si aplica)

            // Cédula de quien registró el pesaje
            $table->string('cedula_asignador', 20);
            $table->foreign('cedula_asignador')->references('cedula')->on('personas')->restrictOnDelete();

            // Medicamento opcionalmente asociado al pesaje (si fue una visita médica)
            $table->unsignedBigInteger('id_medicamento')->nullable();
            $table->foreign('id_medicamento')->references('id_medicamento')->on('medicamentos')->nullOnDelete();

            $table->decimal('confianza', 5, 4)->nullable();   // confianza del modelo IA
            $table->json('caja_deteccion')->nullable();        // bbox YOLO
            $table->string('tamano', 50)->nullable();          // pequeño/mediano/grande
            $table->text('notas')->nullable();
            $table->string('foto')->nullable();                // ruta de la foto
            $table->timestamp('fecha_de_foto')->useCurrent();
            $table->enum('metodo', ['fotografia', 'manual']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_animal');
    }
};