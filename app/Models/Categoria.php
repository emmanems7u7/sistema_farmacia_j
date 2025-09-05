<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'estado'];

    public function catalogos()
    {
        return $this->hasMany(Catalogo::class);
    }
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
     // Relación 1 a 1 con CategoriaIcono
    public function icono()
    {
        return $this->hasOne(CategoriaIcono::class, 'categoria_id');
    }
}
