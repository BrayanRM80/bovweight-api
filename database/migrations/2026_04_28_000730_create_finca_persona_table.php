<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finca_persona', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_finca');
            $table->foreign('id_finca')->references('id_finca')->on('fincas')->cascadeOnDelete();

            $table->string('cedula', 20);
            $table->foreign('cedula')->references('cedula')->on('personas')->cascadeOnDelete();

            $table->boolean('es_dueno')->default(false);
            $table->timestamps();

            $table->unique(['id_finca', 'cedula']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finca_persona');
    }
};