<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Producto extends Model
{


    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'stock_minimo',
        'stock_maximo',
        'stock',
        'categoria_id',
        'laboratorio_id',
        'imagen'

    ];
    use HasFactory;

    // eelación con la tabla 'categorias' (muchos productos pueden pertenecer a una categoría)
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    // relación con la tabla 'laboratorios' (muchos productos pueden pertenecer a un laboratorio)
    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class);
    }


    //un producto puede permanecer a varias compraas
    public function compras()
    {
        return $this->hasMany(Compras::class);
    }

    public function proveedors()
    {
        return $this->belongsToMany(Proveedor::class, 'producto_proveedors')->withTimestamps();
    }
    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'fecha_vencimiento' => 'datetime',
    ];


    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }





    // Stock total (suma de todos los lotes)
    public function getStockAttribute()
    {
        return $this->lotes->sum('cantidad');
    }


    public function getStockTotalAttribute()
    {
        return $this->lotes()->sum('cantidad');
    }
    // Fecha de vencimiento más cercana (opcional)
    public function getFechaVencimientoProximaAttribute()
    {
        return $this->lotes()->min('fecha_vencimiento');
    }

    public function detalleCompra()
    {
        return $this->hasMany(\App\Models\DetalleCompra::class);
    }

    public function detalleVenta()
    {
        return $this->hasMany(\App\Models\DetalleVenta::class);
    }
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
        // Si el nombre de la clave foránea es diferente:
        // return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }



    // Método para verificar stock
    public function tieneStock()
    {
        return $this->stock > 0;
    }

    // Método para verificar si está agotado
    public function estaAgotado()
    {
        return $this->stock <= 0;
    }


    public function lotesActivos()
    {
        return $this->hasMany(Lote::class)
            ->where('cantidad', '>', 0)
            ->where(function ($query) {
                $query->whereNull('fecha_vencimiento')
                    ->orWhere('fecha_vencimiento', '>=', now());
            });
    }
}
