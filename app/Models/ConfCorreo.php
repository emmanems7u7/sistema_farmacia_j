<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfCorreo extends Model
{
    protected $fillable = [
        'conf_protocol',
        'conf_smtp_host',
        'conf_smtp_port',
        'conf_smtp_user',
        'conf_smtp_pass',
        'conf_mailtype',
        'conf_charset',
        'conf_in_background',
        'accion_usuario',
        'accion_fecha',
    ];
}
