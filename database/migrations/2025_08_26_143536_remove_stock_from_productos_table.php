<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Elimina la columna stock de productos.
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }

    /**
     * Restaura la columna stock en caso de rollback.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->integer('stock')->nullable();
        });
    }
};
