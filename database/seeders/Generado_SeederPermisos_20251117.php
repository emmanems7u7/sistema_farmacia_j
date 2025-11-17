<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class Generado_SeederPermisos_20251117 extends Seeder
{
    public function run()
    {
        $permisos = [
            ['id' => 119, 'name' => 'alertas', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 118, 'name' => 'caja.eliminar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 117, 'name' => 'compras.eliminar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 116, 'name' => 'ventas.eliminar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 115, 'name' => 'clientes.eliminar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 114, 'name' => 'clientes.actualizar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 113, 'name' => 'clientes.editar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 112, 'name' => 'clientes.guardar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 111, 'name' => 'clientes.ver', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 110, 'name' => 'clientes.crear', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 109, 'name' => 'categorias.ver', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 108, 'name' => 'productos.eliminar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 107, 'name' => 'productos.actualizar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 106, 'name' => 'productos .editar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 105, 'name' => 'productos.ver', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 104, 'name' => 'productos.guardar', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
            ['id' => 103, 'name' => 'productos.crear', 'tipo' => 'permiso', 'id_relacion' => -1, 'guard_name' => 'web' ],
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(
                ['name' => $permiso['name'], 'tipo' => $permiso['tipo']],
                $permiso
            );
        }
    }
}