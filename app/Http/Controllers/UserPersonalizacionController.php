<?php

namespace App\Http\Controllers;

use App\Models\UserPersonalizacion;
use Illuminate\Http\Request;

class UserPersonalizacionController extends Controller
{
    public function guardarSidebarColor(Request $request)
    {
        $request->validate([
            'color' => 'required|string|in:primary,dark,info,success,warning,danger',
        ]);

        $user = auth()->user();

        $user->preferences()->updateOrCreate(
            ['user_id' => $user->id],
            ['sidebar_color' => $request->color]
        );


        return response()->json(['success' => true]);
    }

    public function updateSidebarType(Request $request)
    {
        $request->validate([
            'sidebar_type' => 'required|string|in:bg-white,bg-default,bg-transparent', // ejemplo validación
        ]);

        $user = auth()->user();

        // Suponiendo que UserPersonalizacion está relacionado con User
        $user->preferences()->updateOrCreate([], [
            'sidebar_type' => $request->sidebar_type,
        ]);

        return response()->json(['message' => 'Sidebar type actualizado']);
    }

    public function updateDark(Request $request)
    {


        $user = auth()->user();
        $personalizacion = UserPersonalizacion::where('user_id', $user->id)->first();
        $personalizacion->dark_mode = $request->dark_mode ? 1 : 0;
        $personalizacion->save();
        return response()->json(['success' => true]);
    }
}
