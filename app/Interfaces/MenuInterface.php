<?php

namespace App\Interfaces;

interface MenuInterface
{
    public function CrearMenu($request);
    public function CrearSeccion($request);
    public function ObtenerMenuPorSeccion($seccion_id);
}
