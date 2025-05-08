<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Catalogo;

class CatalogoSeeder extends Seeder
{
    public function run()
    {

        foreach (range(1, 10) as $categoriaId) {

            Catalogo::create([
                'categoria_id' => rand(1, 4),
                'catalogo_parent' => null,
                'catalogo_codigo' => 'C-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'catalogo_descripcion' => 'Descripción del catálogo ' . $categoriaId,
                'catalogo_estado' => rand(0, 1),
                'accion_usuario' => 'sistema',
            ]);
        }
    }
}
