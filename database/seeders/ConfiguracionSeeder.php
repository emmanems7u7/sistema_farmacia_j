<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('configuracion')->insert([
            'doble_factor_autenticacion' => '0',
            'limite_de_sesiones' => '2',
            'created_at' => Carbon::parse('2025-04-08 09:18:32'),
            'updated_at' => Carbon::parse('2025-04-16 15:15:18'),
        ]);
    }
}
