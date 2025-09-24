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
         Schema::table('laboratorios', function (Blueprint $table) {
        $table->string('nit')->nullable()->after('telefono');
        $table->string('correo')->nullable()->after('nit');
        $table->string('nombre_proveedor')->nullable()->after('correo');
        $table->string('celular')->nullable()->after('nombre_proveedor');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratorios', function (Blueprint $table) {
        $table->dropColumn(['nit', 'correo', 'nombre_proveedor', 'celular']);
    });
    }
};
