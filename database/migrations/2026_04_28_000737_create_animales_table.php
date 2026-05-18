<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('animales', function (Blueprint $table) {
            $table->string('numero_arete', 50)->primary();
            $table->string('nombre', 100)->nullable();

            $table->unsignedBigInteger('id_finca');
            $table->foreign('id_finca')->references('id_finca')->on('fincas')->cascadeOnDelete();

            $table->enum('sexo', ['macho', 'hembra']);

            $table->unsignedBigInteger('id_estado');
            $table->foreign('id_estado')->references('id_estado')->on('estados_animal')->restrictOnDelete();

            $table->string('raza', 50);
            $table->date('fecha_nacimiento')->nullable();
            $table->text('nota_general')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes('borrado_logico_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animales');
    }
};