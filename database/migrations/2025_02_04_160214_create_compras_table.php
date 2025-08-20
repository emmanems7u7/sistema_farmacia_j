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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('comprobante');
            $table->decimal('precio_total',10,2);
            

        $table->unsignedBigInteger('sucursal_id');
        $table->unsignedBigInteger('laboratorio_id');
        $table->foreign('laboratorio_id')->references('id')->on('laboratorios')->onDelete('cascade');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
