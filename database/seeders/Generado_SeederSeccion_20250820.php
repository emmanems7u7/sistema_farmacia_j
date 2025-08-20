<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seccion;

class Generado_SeederSeccion_20250820 extends Seeder
{
    public function run(): void
    {
        $secciones = [
            [
                'id' => 13,
                'titulo' => 'Reportes',
                'icono' => 'fas fa-chart-bar',
                'posicion' => 7,
                'accion_usuario' => '',
            ],
            [
                'id' => 12,
                'titulo' => 'Operaciones',
                'icono' => 'fas fa-cogs',
                'posicion' => 6,
                'accion_usuario' => '',
            ],            [
                'id' => 11,
                'titulo' => 'Inventario',
                'icono' => 'fas fa-boxes',
                'posicion' => 5,
                'accion_usuario' => '',
            ],];

        foreach ($secciones as $data) {
            Seccion::firstOrCreate(
                ['titulo' => $data['titulo']],
                $data
            );
        }
    }
}