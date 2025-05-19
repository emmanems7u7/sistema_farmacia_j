<?php

namespace App\Http\Controllers;
use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function edit()
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Configuracion', 'url' => route('admin.configuracion.edit')],
        ];
        $config = Configuracion::first();
        return view('configuracion.configuracion_general', compact('config', 'breadcrumb'));
    }

    public function update(Request $request)
    {

        $config = Configuracion::first();

        $config->update([
            'doble_factor_autenticacion' => $request->has('doble_factor_autenticacion'),
            'limite_de_sesiones' => $request->input('limite_de_sesiones'),
            'GROQ_API_KEY' => $request->input('GROQ_API_KEY'),
        ]);

        return redirect()->back()->with('success', 'Configuración actualizada.');
    }
}
