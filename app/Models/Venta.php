<?php

namespace App\Models;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    //
    protected $fillable = [
        'fecha',
        'precio_total',
        'cliente_id',
        'usuario_id',
        'sucursal_id',
        // otros campos...
    ];



    

    use HasFactory, HasRoles;

    //una ccompra tiene muchos detalle de esa compra
    public function detallesVenta(){
        return $this->hasMany(DetalleVenta::class);

    }

    public function cliente()
{
    return $this->belongsTo(Cliente::class);
}


public function movimientosCaja()
{
    return $this->hasOne(MovimientoCaja::class, 'venta_id');
}



 // Relación con el usuario/vendedor
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

   


     public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
        // Si el nombre de la clave foránea es diferente:
        // return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }
}
