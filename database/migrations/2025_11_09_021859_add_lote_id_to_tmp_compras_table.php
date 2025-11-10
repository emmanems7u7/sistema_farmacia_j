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
        $table->foreignId('lote_id')->nullable()->constrained('lotes');
    });
}

public function down()
{
    Schema::table('tmp_compras', function (Blueprint $table) {
        $table->dropForeign(['lote_id']);
        $table->dropColumn('lote_id');
    });
}
};
