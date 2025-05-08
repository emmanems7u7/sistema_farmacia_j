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
        Schema::create('catalogos', function (Blueprint $table) {
            $table->id();
            $table->integer('catalogo_parent')->nullable()
                ->comment('Si la lista depende de algún registro (p.ej. Departamento => Ciudad), se registra el ID del registro padre');
            $table->string('catalogo_codigo', 50)
                ->comment('Código identificador del elemento dentro del catálogo');
            $table->string('catalogo_descripcion', 100)
                ->comment('Descripción del elemento del catálogo');
            $table->integer('catalogo_estado')->default(1)
                ->comment('Estado del catálogo: 1=activo, 0=inactivo');
            $table->string('accion_usuario', 20)->nullable()
                ->comment('Nombre de usuario que realizó la última modificación');
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogos');
    }
};
