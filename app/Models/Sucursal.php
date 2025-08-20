<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
        'imagen',
        'estado',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }
}