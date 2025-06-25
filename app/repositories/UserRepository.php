<?php
namespace App\Repositories;

use App\Interfaces\UserInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UserRepository extends BaseRepository implements UserInterface
{
    public function __construct()
    {
        parent::__construct();

    }
    public function CrearUsuario($request)
    {
        $this->validar_datos($request);

        $user = User::create(attributes: [
            'name' => $this->cleanHtml($request->input('name')),
            'email' => $this->cleanHtml($request->input('email')),
            'password' => Hash::make($this->configuracion->conf_defecto),
            'usuario_fecha_ultimo_acceso' => null,
            'usuario_fecha_ultimo_password' => null,
            'usuario_nombres' => $this->cleanHtml($request->input('usuario_nombres')),
            'usuario_app' => $this->cleanHtml($request->input('usuario_app')),
            'usuario_apm' => $this->cleanHtml($request->input('usuario_apm')),
            'usuario_telefono' => $this->cleanHtml($request->input('usuario_telefono')),
            'usuario_direccion' => $this->cleanHtml($request->input('usuario_direccion')),
            'accion_fecha' => now(),
            'accion_usuario' => Auth::user()->name,
            'usuario_activo' => 1,
        ]);
        return $user;
    }
    public function EditarUsuario($request, $id, $perfil)
    {

        $this->validar_datos($request, $id);
        $user = User::findOrFail($id);
        if ($perfil == 1) {

            $request->validate([
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($request->hasFile('profile_picture')) {


                $file = $request->file('profile_picture');


                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();


                $destinationPath = public_path('uploads/imagenes/fotos_perfiles');

                // verifica si carpeta existe, si no la crea
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0775, true);
                }


                $file->move($destinationPath, $fileName);
                $foto_perfil = 'uploads/imagenes/fotos_perfiles/' . $fileName;
            } else {
                $foto_perfil = $user->foto_perfil;

            }

        } else {
            $foto_perfil = $user->foto_perfil;
        }

        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'usuario_nombres' => $request->input('usuario_nombres'),
            'usuario_app' => $request->input('usuario_app'),
            'usuario_apm' => $request->input('usuario_apm'),
            'usuario_telefono' => $request->input('usuario_telefono'),
            'usuario_direccion' => $request->input('usuario_direccion'),
            'accion_fecha' => now(),
            'accion_usuario' => Auth::user()->name,
            'usuario_activo' => 1,
            'foto_perfil' => $foto_perfil,
        ]);
        return $user;
    }

    public function EditarDatosPersonales($request, $id)
    {
        $user = User::findOrFail($id);

        $this->validar_datos($request, $id);

        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'usuario_nombres' => $request->input('usuario_nombres'),
            'usuario_app' => $request->input('usuario_app'),
            'usuario_apm' => $request->input('usuario_apm'),
            'usuario_telefono' => $request->input('usuario_telefono'),
            'usuario_direccion' => $request->input('usuario_direccion'),

            'accion_fecha' => now(),
            'accion_usuario' => Auth::user()->name,
            'usuario_activo' => 1,
        ]);
    }

    public function GetUsuario($id)
    {
        $user = User::find($id);

        if ($user) {

            return response()->json([
                'name' => $user->name,
                'email' => $user->email,
                'usuario_nombres' => $user->usuario_nombres,
                'usuario_app' => $user->usuario_app,
                'usuario_apm' => $user->usuario_apm,
                'usuario_telefono' => $user->usuario_telefono,
                'usuario_direccion' => $user->usuario_direccion,
            ]);
        } else {

            return response()->json(['error' => 'Datos no encontrados'], 404);
        }
    }
    public function GetUsuarios()
    {

    }

    function validar_datos($request, $user_id = null)
    {
        $email_validacion = 'required|email|not_regex:/<\s*script/i';

        if ($user_id) {
            $email_validacion .= '|unique:users,email,' . $user_id;
        } else {
            $email_validacion .= '|unique:users,email';
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|not_regex:/<\s*script/i',
            'email' => $email_validacion,
            'usuario_nombres' => 'required|string|max:100|not_regex:/<\s*script/i',
            'usuario_app' => 'required|string|max:50|not_regex:/<\s*script/i',
            'usuario_apm' => 'required|string|max:50|not_regex:/<\s*script/i',
            'usuario_telefono' => 'required|regex:/^[1-9][0-9]*$/',
            'usuario_direccion' => 'required|string|max:1000|not_regex:/<\s*script/i',
            'role' => 'required|exists:roles,name',
        ]);

    }

}
