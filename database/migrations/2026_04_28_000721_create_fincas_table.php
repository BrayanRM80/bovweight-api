<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fincas', function (Blueprint $table) {
            $table->id('id_finca');
            $table->string('nombre', 100);
            $table->string('ubicacion', 150)->nullable();
            $table->decimal('hectareas', 8, 2)->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes('borrado_logico_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fincas');
    }
};