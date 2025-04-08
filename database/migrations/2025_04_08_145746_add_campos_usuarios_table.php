<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('usuario_fecha_ultimo_acceso')
                ->nullable()
                ->after('password')
                ->comment('Fecha y hora del último acceso exitoso');

            $table->dateTime('usuario_fecha_ultimo_password')
                ->nullable()
                ->after('usuario_fecha_ultimo_acceso')
                ->comment('Fecha y hora del último cambio de contraseña');

            $table->string('usuario_nombres', 100)
                ->nullable()
                ->after('usuario_fecha_ultimo_password')
                ->comment('Nombres del usuario');

            $table->string('usuario_app', 50)
                ->nullable()
                ->after('usuario_nombres')
                ->comment('Apellido paterno');

            $table->string('usuario_apm', 50)
                ->nullable()
                ->after('usuario_app')
                ->comment('Apellido materno');

            $table->string('usuario_telefono', 100)
                ->nullable()
                ->after('usuario_apm')
                ->comment('Teléfono del usuario');

            $table->string('usuario_direccion', 1000)
                ->nullable()
                ->after('usuario_telefono')
                ->comment('Dirección del usuario');

            $table->dateTime('accion_fecha')
                ->nullable()
                ->after('usuario_direccion')
                ->comment('Fecha de última acción');

            $table->string('accion_usuario', 20)
                ->nullable()
                ->after('accion_fecha')
                ->comment('Usuario que realizó la acción');

            $table->unsignedInteger('usuario_activo')
                ->default(1)
                ->after('accion_usuario')
                ->comment('1 = activo, 0 = inactivo');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn([
                'usuario_fecha_ultimo_acceso',
                'usuario_fecha_ultimo_password',
                'usuario_nombres',
                'usuario_app',
                'usuario_apm',
                'usuario_telefono',
                'usuario_direccion',
                'accion_fecha',
                'accion_usuario',
                'usuario_activo',
            ]);
        });
    }
};
