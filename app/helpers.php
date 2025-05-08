<?php

use App\Models\Configuracion;

if (!function_exists('twoFactorGlobalEnabled')) {
    function twoFactorGlobalEnabled(): bool
    {
        return Configuracion::first()?->doble_factor_autenticacion ?? false;
    }
}
