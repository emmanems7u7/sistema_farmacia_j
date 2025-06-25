<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
class CategoriaSeeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('categorias')->insert([
            [
                'nombre' => 'General',
                'descripcion' => 'Categorías sin clasificación específica',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Departamentos',
                'descripcion' => 'categoria de departamentos',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Ciudades',
                'descripcion' => 'Listado de ciudades',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Inactiva',
                'descripcion' => 'Ejemplo de categoría deshabilitada',
                'estado' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
