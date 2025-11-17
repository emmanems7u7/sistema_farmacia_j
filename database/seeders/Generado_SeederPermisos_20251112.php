<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class Generado_SeederPermisos_20251112 extends Seeder
{
    public function run()
    {
        $permisos = [
            ['id' => 102, 'name' => 'laboratorio.guardar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 101, 'name' => 'laboratorio.editar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 100, 'name' => 'laboratorio.eliminar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 99, 'name' => 'laboratorio.actualizar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 98, 'name' => 'laboratorio.ver', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 97, 'name' => 'categorias.actualizar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 96, 'name' => 'categorias.guardar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 95, 'name' => 'categorias.eliminar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 94, 'name' => 'categorias.editar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 93, 'name' => 'categorias.crear', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 92, 'name' => 'laboratorio.crear', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 91, 'name' => 'ventas.crear', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(
                ['name' => $permiso['name'], 'tipo' => $permiso['tipo']],
                $permiso
            );
        }
    }
}