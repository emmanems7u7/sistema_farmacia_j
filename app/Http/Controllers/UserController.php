<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Mews\Purifier\Facades\Purifier;

class UserController extends Controller
{
    // Mostrar el listado de usuarios
    public function index()
    {
        $users = User::all(); // Trae todos los usuarios
        return view('usuarios.index', compact('users'));
    }

    // Mostrar el formulario para crear un nuevo usuario
    public function create()
    {
        return view('users.create');
    }

    // Almacenar un nuevo usuario
    public function store(Request $request)
    {
        session(['form_action' => 'store']);
        $this->validar_datos($request);

        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make('COD123'),
            'usuario_fecha_ultimo_acceso' => now(),
            'usuario_fecha_ultimo_password' => now(),
            'usuario_nombres' => $request->input('usuario_nombres'),
            'usuario_app' => $request->input('usuario_app'),
            'usuario_apm' => $request->input('usuario_apm'),
            'usuario_telefono' => $request->input('usuario_telefono'),
            'usuario_direccion' => $request->input('usuario_direccion'),
            'accion_fecha' => now(),
            'accion_usuario' => Auth::user()->name,
            'usuario_activo' => 1,
        ]);

        // Redirigir al usuario con un mensaje de éxito
        return redirect()->route('users.index')->with('success', 'Usuario registrado exitosamente!');
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
        ]);


        $validated['name'] = Purifier::clean($validated['name']);
        $validated['email'] = Purifier::clean($validated['email']);
        $validated['usuario_nombres'] = Purifier::clean($validated['usuario_nombres']);
        $validated['usuario_app'] = Purifier::clean($validated['usuario_app']);
        $validated['usuario_apm'] = Purifier::clean($validated['usuario_apm']);
        $validated['usuario_direccion'] = Purifier::clean($validated['usuario_direccion']);
    }

    // Mostrar la información de un usuario
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    // Mostrar el formulario para editar un usuario
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    // Actualizar un usuario
    public function update(request $request, $id)
    {
        session(['form_action' => 'update']);
        session(['user_id' => $id]);
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


        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente!');
    }

    // Eliminar un usuario
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente');
    }

    function GetUsuario($id)
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
}
