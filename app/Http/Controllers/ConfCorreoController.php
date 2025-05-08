<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\ConfCorreo;
use Illuminate\Support\Facades\Auth;
use App\Mail\CorreoPrueba;
use Illuminate\Support\Facades\Mail;
use App\Models\PlantillaCorreo;
use App\Mail\CorreoDesdePlantilla;
use App\Interfaces\CorreoInterface;

class ConfCorreoController extends Controller
{
    protected $correoRepository;
    public function __construct(CorreoInterface $CorreoInterface)
    {

        $this->correoRepository = $CorreoInterface;
    }

    function index()
    {
        $conf_correo = ConfCorreo::find(env('CONF_CORREO_ID'));


        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Configuracion', 'url' => route('configuracion.correo.index')],
        ];
        return view('configuracion.configuracion_correo', compact('breadcrumb', 'conf_correo'));
    }

    function store(request $request)
    {
        $correoId = env('CONF_CORREO_ID');

        $validated = $request->validate([
            'conf_correo_protocol' => 'required|string|max:20|not_in:-1',
            'conf_smtp_host' => 'required|string|max:150',
            'conf_smtp_port' => 'required|integer',
            'conf_smtp_user' => 'required|string|max:150',
            'conf_smtp_pass' => 'required|string|max:150',
            'conf_mailtype' => 'required|string|in:html,text|not_in:-1',
            'conf_charset' => 'required|string|max:20',
            'conf_in_background' => 'required|boolean|not_in:-1',
        ], [
            'conf_correo_protocol.required' => 'El protocolo es obligatorio.',
            'conf_correo_protocol.string' => 'El protocolo debe ser una cadena de texto.',
            'conf_correo_protocol.max' => 'El protocolo no puede superar los 20 caracteres.',
            'conf_correo_protocol.not_in' => 'Por favor, seleccione un protocolo válido.',

            'conf_smtp_host.required' => 'El servidor SMTP es obligatorio.',
            'conf_smtp_host.string' => 'El servidor SMTP debe ser una cadena de texto.',
            'conf_smtp_host.max' => 'El servidor SMTP no puede superar los 150 caracteres.',

            'conf_smtp_port.required' => 'El puerto SMTP es obligatorio.',
            'conf_smtp_port.integer' => 'El puerto SMTP debe ser un número entero.',

            'conf_smtp_user.required' => 'El usuario SMTP es obligatorio.',
            'conf_smtp_user.string' => 'El usuario SMTP debe ser una cadena de texto.',
            'conf_smtp_user.max' => 'El usuario SMTP no puede superar los 150 caracteres.',

            'conf_smtp_pass.required' => 'La contraseña SMTP es obligatoria.',
            'conf_smtp_pass.string' => 'La contraseña SMTP debe ser una cadena de texto.',
            'conf_smtp_pass.max' => 'La contraseña SMTP no puede superar los 150 caracteres.',

            'conf_mailtype.required' => 'El tipo de correo es obligatorio.',
            'conf_mailtype.string' => 'El tipo de correo debe ser una cadena de texto.',
            'conf_mailtype.not_in' => 'Por favor, seleccione un tipo de correo válido.',

            'conf_charset.required' => 'El charset es obligatorio.',
            'conf_charset.string' => 'El charset debe ser una cadena de texto.',
            'conf_charset.max' => 'El charset no puede superar los 20 caracteres.',

            'conf_in_background.required' => 'Es obligatorio especificar si el correo se enviará en segundo plano.',
            'conf_in_background.not_in' => 'Por favor, seleccione una opción válida para el envío en segundo plano.',
        ]);

        $this->correoRepository->EditarConfCorreo($correoId, $request);

        return redirect()->back()->with('success', 'Configuración de correo guardada correctamente.');


    }

    public function enviarPrueba()
    {
        $correo = env('CONF_CORREO_ID');

        $conf = ConfCorreo::find($correo);

        if (!$conf) {
            return response()->json(['error' => 'Configuración no encontrada'], 404);
        }


        config([
            'mail.mailers.smtp.host' => $conf->conf_smtp_host,
            'mail.mailers.smtp.port' => $conf->conf_smtp_port,
            'mail.mailers.smtp.username' => $conf->conf_smtp_user,
            'mail.mailers.smtp.password' => $conf->conf_smtp_pass,
            'mail.mailers.smtp.encryption' => $conf->conf_protocol,
            'mail.default' => 'smtp',
        ]);

        // Enviar el correo de prueba
        // Mail::to($conf->conf_smtp_user)->send(new CorreoPrueba('Este es un correo de prueba.'));

        $this->enviarCorreoPrueba(1, 'emmanuelz7u7@gmail.com');

        return response()->json(['mensaje' => 'Correo enviado correctamente.']);
    }


    public function enviarCorreoPrueba($plantillaId, $destinatario)
    {

        $plantilla = PlantillaCorreo::find($plantillaId);

        if (!$plantilla) {
            return response()->json(['error' => 'Plantilla no encontrada.'], 404);
        }


        $datos = [
            'nombre' => 'Juan Pérez',
            'email' => 'juan.perez@example.com',
            'fecha_registro' => '2025-04-11',
        ];


        $contenidoConDatos = $this->reemplazarVariables($plantilla->contenido, $datos);
        // Enviar el correo utilizando el Mailable
        Mail::to($destinatario)->send(new CorreoDesdePlantilla($plantilla->asunto, $contenidoConDatos));

        return response()->json(['success' => 'Correo enviado con éxito.']);
    }

    private function reemplazarVariables($contenido, $datos)
    {

        foreach ($datos as $key => $value) {
            $contenido = str_replace("{{ $key }}", $value, $contenido);
        }

        return $contenido;
    }
}
