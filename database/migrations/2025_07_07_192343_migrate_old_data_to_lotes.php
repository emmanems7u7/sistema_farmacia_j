<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Producto;  // Asegúrate de que el namespace sea correcto
use App\Models\Lote;

return new class extends Migration {
    public function up()
    {

        // Obtener productos que tenían stock > 0 (para evitar lotes vacíos)
        $productos = Producto::where('stock', '>', 0)->get();

        // Migrar cada producto a la tabla 'lotes'
        foreach ($productos as $producto) {
            Lote::create([
                'producto_id' => $producto->id,

                'numero_lote' => 'LOTE-INICIAL-' . $producto->id,
                'fecha_ingreso' => now(),
                'fecha_vencimiento' => $producto->fecha_vencimiento ?? now()->addYear(),
                'cantidad' => $producto->stock ?? 0,
                'precio_compra' => $producto->precio_compra ?? null,
                'precio_venta' => $producto->precio_venta ?? null,
            ]);
        }
    }

    public function down()
    {
        // Opcional: Revertir la migración eliminando los lotes creados
        Lote::where('numero_lote', 'like', 'LOTE-INICIAL-%')->delete();
    }
};