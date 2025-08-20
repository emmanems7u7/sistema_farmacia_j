<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')->nullable(); // ⬅️ Aquí se hace opcional
            $table->string('numero_lote');
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->integer('cantidad');
            $table->decimal('precio_compra', 8, 2)->nullable();
            $table->decimal('precio_venta', 8, 2)->nullable();
            $table->timestamps();

            // Relación con la tabla productos
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
