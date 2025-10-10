<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            //
            // Hacerlo nullable temporalmente para ventas existentes
            $table->foreignId('lote_id')->nullable()->after('producto_id')
                  ->constrained('lotes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            //
              $table->dropForeign(['lote_id']);
            $table->dropColumn('lote_id');
        });
    }
};
