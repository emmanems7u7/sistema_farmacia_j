<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('lotes', function (Blueprint $table) {
            // Añadir campo 'activo' (boolean)
            $table->boolean('activo')->default(true)->after('cantidad');

            // Añadir campo 'cantidad_inicial' (integer)
            $table->integer('cantidad_inicial')->after('cantidad');
        });

        // Actualizar lotes existentes: cantidad_inicial = cantidad, activo = true si hay stock
        DB::statement('UPDATE lotes SET cantidad_inicial = cantidad, activo = (cantidad > 0)');
    }

    public function down()
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->dropColumn(['activo', 'cantidad_inicial']);
        });
    }
};