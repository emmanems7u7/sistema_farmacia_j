<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class Generado_SeederPermisos_20250820 extends Seeder
{
    public function run()
    {
        $permisos = [
            ['id' => 87, 'name' => 'Catálogo', 'tipo' => 'menu', 'id_relacion' => 26, 'guard_name' => 'web' ],
            ['id' => 86, 'name' => 'Inventarios', 'tipo' => 'menu', 'id_relacion' => 25, 'guard_name' => 'web' ],
            ['id' => 85, 'name' => 'Reporte de Egresos', 'tipo' => 'menu', 'id_relacion' => 24, 'guard_name' => 'web' ],
            ['id' => 84, 'name' => 'Reporte de Ingresos', 'tipo' => 'menu', 'id_relacion' => 23, 'guard_name' => 'web' ],
            ['id' => 83, 'name' => 'Reportes', 'tipo' => 'seccion', 'id_relacion' => 13, 'guard_name' => 'web' ],
            ['id' => 82, 'name' => 'Caja', 'tipo' => 'menu', 'id_relacion' => 21, 'guard_name' => 'web' ],
            ['id' => 81, 'name' => 'Ventas', 'tipo' => 'menu', 'id_relacion' => 20, 'guard_name' => 'web' ],
            ['id' => 80, 'name' => 'Compras', 'tipo' => 'menu', 'id_relacion' => 19, 'guard_name' => 'web' ],
            ['id' => 79, 'name' => 'Operaciones', 'tipo' => 'seccion', 'id_relacion' => 12, 'guard_name' => 'web' ],
            ['id' => 78, 'name' => 'Clientes', 'tipo' => 'menu', 'id_relacion' => 18, 'guard_name' => 'web' ],
            ['id' => 77, 'name' => 'Gestionar Lotes', 'tipo' => 'menu', 'id_relacion' => 17, 'guard_name' => 'web' ],
            ['id' => 76, 'name' => 'Productos', 'tipo' => 'menu', 'id_relacion' => 16, 'guard_name' => 'web' ],
            ['id' => 75, 'name' => 'Proveedores', 'tipo' => 'menu', 'id_relacion' => 15, 'guard_name' => 'web' ],
            ['id' => 74, 'name' => 'Laboratorio', 'tipo' => 'menu', 'id_relacion' => 14, 'guard_name' => 'web' ],
            ['id' => 72, 'name' => 'Inventario', 'tipo' => 'seccion', 'id_relacion' => 11, 'guard_name' => 'web' ],
            ['id' => 71, 'name' => 'Categorias', 'tipo' => 'menu', 'id_relacion' => 12, 'guard_name' => 'web' ],
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(
                ['name' => $permiso['name'], 'tipo' => $permiso['tipo']],
                $permiso
            );
        }
    }
}