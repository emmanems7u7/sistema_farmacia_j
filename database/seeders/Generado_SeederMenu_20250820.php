<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class Generado_SeederMenu_20250820 extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'id' => '26',
                'nombre' => 'Catálogo',
                'orden' => 4,
                'padre_id' => null,
                'seccion_id' => 13,
                'ruta' => 'admin.catalogo.index',
            ],
            [
                'id' => '25',
                'nombre' => 'Inventarios',
                'orden' => 3,
                'padre_id' => null,
                'seccion_id' => 13,
                'ruta' => 'admin.inventario.index',
            ],
            [
                'id' => '24',
                'nombre' => 'Reporte de Egresos',
                'orden' => 2,
                'padre_id' => null,
                'seccion_id' => 13,
                'ruta' => 'admin.reporte.egresos.index',
            ],
            [
                'id' => '23',
                'nombre' => 'Reporte de Ingresos',
                'orden' => 1,
                'padre_id' => null,
                'seccion_id' => 13,
                'ruta' => 'admin.reporte.ingresos.index',
            ],
            [
                'id' => '21',
                'nombre' => 'Caja',
                'orden' => 3,
                'padre_id' => null,
                'seccion_id' => 12,
                'ruta' => 'admin.cajas.index',
            ],
            [
                'id' => '20',
                'nombre' => 'Ventas',
                'orden' => 2,
                'padre_id' => null,
                'seccion_id' => 12,
                'ruta' => 'admin.ventas.index',
            ],
            [
                'id' => '19',
                'nombre' => 'Compras',
                'orden' => 1,
                'padre_id' => null,
                'seccion_id' => 12,
                'ruta' => 'admin.compras.index',
            ],
            [
                'id' => '18',
                'nombre' => 'Clientes',
                'orden' => 6,
                'padre_id' => null,
                'seccion_id' => 11,
                'ruta' => 'admin.clientes.index',
            ],
            [
                'id' => '17',
                'nombre' => 'Gestionar Lotes',
                'orden' => 5,
                'padre_id' => null,
                'seccion_id' => 11,
                'ruta' => 'admin.lotes.index',
            ],
            [
                'id' => '16',
                'nombre' => 'Productos',
                'orden' => 4,
                'padre_id' => null,
                'seccion_id' => 11,
                'ruta' => 'admin.productos.index',
            ],
            [
                'id' => '15',
                'nombre' => 'Proveedores',
                'orden' => 3,
                'padre_id' => null,
                'seccion_id' => 11,
                'ruta' => 'admin.proveedores.index',
            ],
            [
                'id' => '14',
                'nombre' => 'Laboratorio',
                'orden' => 2,
                'padre_id' => null,
                'seccion_id' => 11,
                'ruta' => 'admin.laboratorios.index',
            ],
            [
                'id' => '12',
                'nombre' => 'Categorias',
                'orden' => 1,
                'padre_id' => null,
                'seccion_id' => 10,
                'ruta' => 'categorias.index',
            ],
        ];

        foreach ($menus as $data) {
            Menu::firstOrCreate(
                ['nombre' => $data['nombre']],
                $data
            );
        }
    }
}