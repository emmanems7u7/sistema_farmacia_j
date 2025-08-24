<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {



        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('1'),
        ]);

        //Contenido minimo para levantar sistema

        $this->call(class: RolesPermissionsSeeder::class);
        $this->call(UserSeeder::class);
        //$this->call(class: CategoriaSeeeder::class);
        //$this->call(CatalogoSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(ConfiguracionSeeder::class);
        $this->call(ConfCorreoSeeder::class);
        $this->call(SeccionesSeeder::class);
        $this->call(MenusSeeder::class);
        $this->call(ConfiguracionCredencialesSeeder::class);

        // Contenido minimo para levantar sistema



        // Seeders creados automaticamente 20-08-2025

        // SECCION
        $this->call(Generado_SeederSeccion_20250820::class);

        // FIN SECCION


        // MENU
        $this->call(Generado_SeederMenu_20250820::class);

        // FIN MENU

        // PERMISOS
        $this->call(Generado_SeederPermisos_20250820::class);

        // FIN PERMISOS


        // Fin Seeders creados automaticamente 20-08-2025
    }




}