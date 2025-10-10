<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'detalle_ventas';

    // Columnas que se pueden llenar masivamente - AGREGAR LAS NUEVAS COLUMNAS
    protected $fillable = [
        'cantidad',
        'subtotal',       // ← AGREGAR ESTA
        'venta_id',
        'producto_id',
        'lotes_info',     // ← AGREGAR ESTA
    ];

    // Relaciones
    public function venta(){
        return $this->belongsTo(Venta::class);
    }

    public function lote()
{
    return $this->belongsTo(Lote::class);
}
    public function producto(){
        return $this->belongsTo(Producto::class);
    }

    // Casts para formato automático
    protected $casts = [
        'lotes_info' => 'array', // Cast automático de JSON a array
    ];

    // Accesor para lotes_info
    public function getLotesInfoAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }
}