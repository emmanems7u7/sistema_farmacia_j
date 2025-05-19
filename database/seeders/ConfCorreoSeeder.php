<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class ConfCorreoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('conf_correos')->insert([
            'conf_protocol' => 'smtp',
            'conf_smtp_host' => 'smtp.gmail.com',
            'conf_smtp_port' => '465',
            'conf_smtp_user' => 'emmanuelz7u7@gmail.com',
            'conf_smtp_pass' => 'rrqibecftokaandb',
            'conf_mailtype' => 'html',
            'conf_charset' => 'UTF-8',
            'conf_in_background' => '1',
            'accion_usuario' => 'admin',
            'created_at' => Carbon::parse('2025-04-11 15:26:20'),
            'updated_at' => Carbon::parse('2025-04-11 15:47:15'),
        ]);
    }
}
