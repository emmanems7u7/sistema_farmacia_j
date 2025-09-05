<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaIcono extends Model
{
    use HasFactory;

    protected $table = 'categorias_icono';

    protected $fillable = [
        'categoria_id',
        'icon',
        'color',
    ];

    // Relación inversa con Categoria
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }


}
