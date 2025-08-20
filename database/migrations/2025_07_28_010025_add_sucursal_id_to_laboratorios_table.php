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
            // Verificar si la columna no existe antes de agregarla
            if (!Schema::hasColumn('laboratorios', 'sucursal_id')) {
                $table->unsignedBigInteger('sucursal_id')
                      ->nullable()
                      ->after('id'); // Colocación después del ID
                
                // Agregar la clave foránea si existe la tabla sucursales
                if (Schema::hasTable('sucursales')) {
                    $table->foreign('sucursal_id')
                          ->references('id')
                          ->on('sucursales')
                          ->onDelete('set null');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratorios', function (Blueprint $table) {
            // Eliminar la clave foránea primero si existe
            if (Schema::hasColumn('laboratorios', 'sucursal_id')) {
                // Verificar si existe la restricción de clave foránea
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableForeignKeys($table->getTable());
                
                $foreignKeyName = array_reduce($indexes, function($carry, $item) {
                    return $item->getLocalColumns()[0] === 'sucursal_id' ? $item->getName() : $carry;
                });
                
                if ($foreignKeyName) {
                    $table->dropForeign($foreignKeyName);
                }
                
                // Eliminar la columna
                $table->dropColumn('sucursal_id');
            }
        });
    }
};