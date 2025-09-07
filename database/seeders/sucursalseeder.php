<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class sucursalseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sucursals')->insert([
            [
                'nombre'    => 'FARMACIA MARIEL',
                'direccion' => 'ZONA VINO TINTO AV BALTAZAR',
                'telefono'  => '75260345',
                'email'     => 'mariel@gmail.com',
                'imagen'    => 'assets/img/logofarmacia.jpeg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
          
        ]);
    }
}
