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
    Schema::table('detalle_compras', function (Blueprint $table) {
        $table->unsignedBigInteger('lote_id')->after('producto_id');
        $table->foreign('lote_id')->references('id')->on('lotes')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('detalle_compras', function (Blueprint $table) {
        $table->dropForeign(['lote_id']);
        $table->dropColumn('lote_id');
    });
}

};
