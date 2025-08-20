<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TmpCompra extends Model
{
    //
    public function producto(){
        return $this->belongsTo(Producto::class);

    }
}
