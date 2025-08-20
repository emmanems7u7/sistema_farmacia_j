<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('productos', function (Blueprint $table) {
        $table->id();
        $table->string('codigo')->unique();
        $table->string('nombre');
        $table->text('descripcion')->nullable();  
        $table->text('imagen')->nullable();
        $table->unsignedInteger('stock');  
        $table->unsignedInteger('stock_minimo');
        $table->unsignedInteger('stock_maximo');
        $table->decimal('precio_compra', 8, 2);
        $table->decimal('precio_venta', 8, 2);
        $table->date('fecha_ingreso');
        $table->date('fecha_vencimiento')->nullable();

        // Relación con 'categorias'
        $table->unsignedBigInteger('categoria_id');
        $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('cascade');

        // Relación con 'laboratorios'
        $table->unsignedBigInteger('laboratorio_id');
        $table->foreign('laboratorio_id')->references('id')->on('laboratorios')->onDelete('cascade');
        $table->unsignedBigInteger('sucursal_id');
        $table->timestamps();
    });
}

  
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
