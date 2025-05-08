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
        Schema::create('conf_correos', function (Blueprint $table) {
            $table->id();

            $table->string('conf_protocol', 20)
                ->comment('Protocolo de envío de correo');

            $table->string('conf_smtp_host', 150)
                ->comment('Dirección del servidor SMTP.');

            $table->integer('conf_smtp_port')
                ->comment('Puerto utilizado para la conexión SMTP.');

            $table->string('conf_smtp_user', 150)
                ->comment('Usuario utilizado para autenticación SMTP.');

            $table->string('conf_smtp_pass', 150)
                ->comment('Contraseña para la autenticación SMTP.');

            $table->string('conf_mailtype', 20)
                ->comment('Tipo de formato del correo');

            $table->string('conf_charset', 20)
                ->comment('Charset utilizado en el cuerpo del correo');

            $table->boolean('conf_in_background')
                ->default(true)
                ->comment('Establece si el envío del correo se realizará en segundo plano. 0=No; 1=Sí.');

            $table->string('accion_usuario', 50)
                ->comment('Usuario que realizó la acción.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conf_correos');
    }
};
