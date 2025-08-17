<?php
namespace App\Repositories;

use App\Interfaces\CorreoInterface;
use \App\Models\ConfCorreo;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\MockObject\Stub\ReturnReference;

class CorreoRepository extends BaseRepository implements CorreoInterface
{

    public function __construct()
    {
        parent::__construct();

    }
    public function EditarPlantillaCorreo($request, $email)
    {
        $email->nombre = $request->input('nombre_plantilla');
        $email->asunto = $request->input('asunto_plantilla');
        $email->contenido = $request->input('contenido');
        $email->save();
    }
    public function EditarConfCorreo($request)
    {


        $confCorreo = ConfCorreo::updateOrCreate(
            ['id' => 1],
            [
                'mailer' => $request->mailer,
                'host' => $request->host,
                'port' => $request->port,
                'username' => $request->username,
                'password' => $request->password,
                'encryption' => $request->encryption,
                'from_address' => $request->from_address,
                'from_name' => $request->from_name,
            ]
        );

        return $confCorreo;
    }
}
