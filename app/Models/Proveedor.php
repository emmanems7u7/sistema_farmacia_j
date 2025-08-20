<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory, HasRoles;
//UN PROVEEDORE PUEDDE PERMANECER A MUCHAS COMPRAS
    public function compras(){
        return $this->hasMany(Compra::class);

    }
    public function productos()
{
    return $this->belongsToMany(Producto::class, 'producto_proveedors')->withTimestamps();
}
}
