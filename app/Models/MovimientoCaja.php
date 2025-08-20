<?php

namespace App\Models;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{

 protected $fillable = [
        'tipo',
        'monto',
        'descripcion',
        'fecha_movimiento',
        'caja_id',
        // Agrega aquí otros campos que necesites asignar masivamente
    ];


    use HasFactory;
    //un movimiento puede tener un arqueo
public function caja(){
    return $this->belongsTo(Caja::class);
}



public function venta()
{
    return $this->belongsTo(Venta::class, 'venta_id'); // Especifica la clave foránea
}

public function compra()
{
    return $this->belongsTo(Compra::class, 'compra_id'); // Especifica la clave foránea
}
}
