<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class Generado_SeederPermisos_20250824 extends Seeder
{
    public function run()
    {
        $permisos = [
            ['id' => 90, 'name' => 'configuracion.credenciales_ver', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 89, 'name' => 'configuracion.credenciales_actualizar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 88, 'name' => 'Configuración de Credenciales', 'tipo' => 'menu', 'id_relacion' => 27, 'guard_name' => 'web' ],
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(
                ['name' => $permiso['name'], 'tipo' => $permiso['tipo']],
                $permiso
            );
        }
    }
}