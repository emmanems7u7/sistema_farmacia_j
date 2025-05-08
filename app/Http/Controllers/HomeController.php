<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
        ];

        if (Auth::user()->usuario_fecha_ultimo_password) {
            $ultimoCambio = Carbon::parse(Auth::user()->usuario_fecha_ultimo_password);

            $diferenciaDias = (int) $ultimoCambio->diffInDays(Carbon::now());

            if ($diferenciaDias >= 100) {
                $tiempo_cambio_contraseña = 1;
            } else {
                $tiempo_cambio_contraseña = 2;
            }
        } else {
            $tiempo_cambio_contraseña = 0;
        }

        return view('home', compact('breadcrumb', 'tiempo_cambio_contraseña'));
    }

}
