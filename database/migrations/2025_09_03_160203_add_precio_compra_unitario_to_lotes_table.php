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
    Schema::table('lotes', function (Blueprint $table) {
        $table->decimal('precio_compra_unitario', 10, 2)->after('precio_compra')->default(0);
    });
}

public function down()
{
    Schema::table('lotes', function (Blueprint $table) {
        $table->dropColumn('precio_compra_unitario');
    });
}

};
