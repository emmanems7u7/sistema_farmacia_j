<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;



class PasswordController extends Controller
{

    public function update(Request $request)
    {

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Por favor, ingrese su contraseña actual.',
            'current_password.current_password' => 'La contraseña actual es incorrecta.',
            'new_password.required' => 'La nueva contraseña es obligatoria.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.confirmed' => 'Las contraseñas no coinciden.',
        ]);


        if (!Hash::check($request->current_password, Auth::user()->password)) {
            throw ValidationException::withMessages(messages: [
                'current_password' => ['La contraseña actual es incorrecta.'],
            ]);
        }


        Auth::user()->update([
            'password' => Hash::make($request->new_password),
            'usuario_fecha_ultimo_password' => Carbon::now(),
        ]);


        return redirect()->back()->with('status', 'Contraseña actualizada correctamente.');
    }
    function ActualizarContraseña()
    {
        $user = Auth::user();

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Actualizar Contraseña', 'url' => route('user.actualizar.contraseña')],
        ];

        if ($user->usuario_fecha_ultimo_password) {
            $ultimoCambio = Carbon::parse($user->usuario_fecha_ultimo_password);

            $diferenciaDias = (int) $ultimoCambio->diffInDays(Carbon::now());

            if ($diferenciaDias >= 100) {
                $tiempo_cambio_contraseña = "Es tiempo de cambiar tu contraseña.";
            } else {
                $tiempo_cambio_contraseña = "La contraseña está actualizada. Quedan " . (100 - $diferenciaDias) . " días para cambiarla.";
            }
        } else {
            $tiempo_cambio_contraseña = "Nunca se ha cambiado la contraseña. Por favor, actualízala.";
        }
        return view('auth.update_password', compact('tiempo_cambio_contraseña', 'breadcrumb'));
    }
}
