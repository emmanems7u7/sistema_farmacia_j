<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Compra extends Model
{
    //
     protected $fillable = [
        'fecha',
        'comprobante',
        'precio_total',
        'sucursal_id',
        'laboratorio_id'
    ];
    use HasFactory, HasRoles;

    //una ccompra tiene muchos detalle de esa compra
    public function detalles(){
        return $this->hasMany(DetalleCompra::class);

    }

    public function laboratorio()
{
    return $this->belongsTo(Laboratorio::class);
}







public function movimientosCaja()
{
    return $this->hasOne(MovimientoCaja::class, 'compra_id');
}

//public function laboratorio()
  //  {
    //    return $this->belongsTo(Laboratorio::class, 'laboratorio_id');
    //}
    
}



