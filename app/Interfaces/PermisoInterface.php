<?php

namespace App\Interfaces;

interface PermisoInterface
{
    public function GetPermisosTipo($tipo);
    public function GetPermisoTipo($id, $tipo);
    public function GetPermisoMenu($id, $rol_id);
    public function CrearPermiso($request);

    public function EditarPermiso($request, $permission);
}
