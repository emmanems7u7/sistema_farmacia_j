<?php

namespace App\Models;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lote extends Model
{
    protected $fillable = [


     
     
        'cantidad_inicial',
       
        
        
        'activo',
        
        
        'sucursal_id' ,


        'producto_id',
        'numero_lote',
        'fecha_ingreso',
        'fecha_vencimiento',
        'cantidad',
        'precio_compra',  
        'precio_venta',   

         'created_at',
    'updated_at'
    ];
    protected $dates = [

    'fecha_ingreso',
    'fecha_vencimiento',
    'created_at',
    'updated_at'
];



protected $casts = [
    'activo' => 'boolean',
    'fecha_ingreso' => 'datetime',
    'fecha_vencimiento' => 'datetime',
];

    // Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    public function scopeActivos($query)
{
    return $query->where('fecha_vencimiento', '>=', now());
}

public function scopeVencidos($query)
{
    return $query->where('fecha_vencimiento', '<', now());
}

 public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }



 


    public function detalles() {
    return $this->belongsTo(DetalleCompra::class);
}


protected static function boot()
{
    parent::boot();

    static::saved(function ($lote) {
        Cache::forget("producto_{$lote->producto_id}_stock");
    });

    static::deleted(function ($lote) {
        Cache::forget("producto_{$lote->producto_id}_stock");
    });
}



public function scopeInactivos($query)
{
    return $query->where('activo', false);
}



}
