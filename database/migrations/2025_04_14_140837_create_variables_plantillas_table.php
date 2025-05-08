<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('variables_plantillas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->comment('nombre de la variable');
            $table->string('descripcion')->comment('Descripcion de la variable')->nullable();
            $table->string('accion_usuario', 50)
                ->comment('Usuario que realizó la acción.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variables_plantillas');
    }
};
