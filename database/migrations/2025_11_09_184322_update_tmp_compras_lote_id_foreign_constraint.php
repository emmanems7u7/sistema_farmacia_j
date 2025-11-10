<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tmp_compras', function (Blueprint $table) {
            // Eliminar la foreign key existente
            $table->dropForeign(['lote_id']);
            
            // Recrear con onDelete('set null')
            $table->foreign('lote_id')
                  ->references('id')
                  ->on('lotes')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('tmp_compras', function (Blueprint $table) {
            $table->dropForeign(['lote_id']);
            
            // Volver a la constraint original
            $table->foreign('lote_id')
                  ->references('id')
                  ->on('lotes');
                  // Sin onDelete para revertir
        });
    }
};
