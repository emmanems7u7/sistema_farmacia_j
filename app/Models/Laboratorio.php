<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Laboratorio extends Model
{
    use HasFactory, HasRoles;
    

    // Relación con la tabla 'productos' (un laboratorio puede tener muchos productos)
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function compras(){
        return $this->hasMany(Compra::class);

    }
}
