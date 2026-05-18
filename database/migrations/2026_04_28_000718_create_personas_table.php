<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->string('cedula', 20)->primary();
            $table->string('nombre', 100);
            $table->string('apellidos', 150);
            $table->string('correo', 150)->unique();
            $table->string('contrasena');
            $table->string('contacto', 30)->nullable();

            $table->unsignedBigInteger('id_rol');
            $table->foreign('id_rol')->references('id_rol')->on('roles')->restrictOnDelete();

            $table->boolean('activo')->default(true);
            $table->timestamp('correo_verificado_en')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes('borrado_logico_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};