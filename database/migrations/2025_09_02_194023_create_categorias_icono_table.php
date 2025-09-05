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
    Schema::create('categorias_icono', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('categoria_id'); // Relación con categories
        $table->string('icon')->nullable();        // Ícono de la categoría
        $table->string('color')->nullable();       // Color de la categoría
        $table->timestamps();

        $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('cascade');
    });
}

public function down()
{
    Schema::dropIfExists('categorias_icono');
}
};
