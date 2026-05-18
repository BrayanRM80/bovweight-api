<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('medicamentos', function (Blueprint $table) {
            $table->id('id_medicamento');
            $table->string('nombre', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->softDeletes('borrado_logico_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicamentos');
    }
};