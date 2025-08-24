<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class Generado_SeederMenu_20250824 extends Seeder
{
    public function run(): void
    {
        $menus = [            [
                'id' => '27',
                'nombre' => 'Configuración de Credenciales',
                'orden' => 4,
                'padre_id' => null,
                'seccion_id' => 6,
                'ruta' => 'configuracion.credenciales.index',
            ],];

        foreach ($menus as $data) {
            Menu::firstOrCreate(
                ['nombre' => $data['nombre']],
                $data
            );
        }
    }
}