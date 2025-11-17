<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'detalle_ventas';

    // Columnas que se pueden llenar masivamente 
    protected $fillable = [
        'cantidad',
        'subtotal',      
        'venta_id',
        'producto_id',
        'lotes_info',     
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
        'lotes_info' => 'array', 
    ];

    // Accesor para lotes_info
    public function getLotesInfoAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }
}