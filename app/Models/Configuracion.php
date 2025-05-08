<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $fillable = [
        'doble_factor_autenticacion',
        'limite_de_sesiones',
    ];

    protected $table = 'configuracion';
    protected $guarded = [];
}
