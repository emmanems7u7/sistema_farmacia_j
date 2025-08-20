<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn([
                'stock', 
                'fecha_ingreso',
                'fecha_vencimiento',
                'precio_compra', 
                'precio_venta'
            ]);
        });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->integer('stock')->default(0);
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('precio_compra', 8, 2)->nullable();
            $table->decimal('precio_venta', 8, 2)->nullable();
        });
    }
};
