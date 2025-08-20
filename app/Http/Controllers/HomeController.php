<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Sucursal;

use App\Models\Compra;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\DetalleVenta; // Asegúrate de importar el modelo DetalleVenta
use Illuminate\Support\Facades\DB; // Necesario para las consultas
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
use App\Models\ConfiguracionCredenciales;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */


    public function index()
    {


        /*logica Contraseñas */

        $config = ConfiguracionCredenciales::first();
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
        ];

        if (Auth::user()->usuario_fecha_ultimo_password) {
            $ultimoCambio = Carbon::parse(Auth::user()->usuario_fecha_ultimo_password);

            $diferenciaDias = (int) $ultimoCambio->diffInDays(Carbon::now());

            if ($diferenciaDias >= $config->conf_duracion_max) {
                $tiempo_cambio_contraseña = 1;
            } else {
                $tiempo_cambio_contraseña = 2;
            }
        } else {
            $tiempo_cambio_contraseña = 1;
        }



        $total_productos = Producto::count();
        $total_compras = Compra::count();
        $total_clientes = Cliente::count();
        $compras = Compra::count();
        $total_ventas = Venta::count();

        $sucursal_id = Auth::check() ? Auth::user()->sucursal_id : redirect()->route('login')->send();
        $sucursal = Sucursal::where('id', $sucursal_id)->first();

        // Productos más vendidos
        $topProducts = DetalleVenta::join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->where('ventas.sucursal_id', $sucursal_id)
            ->select('productos.nombre', DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'))
            ->groupBy('productos.nombre')
            ->orderBy('total_vendido', 'DESC')
            ->take(5)
            ->get();
        // Preparar datos para la gráfica
        $labels = $topProducts->pluck('nombre');
        $data = $topProducts->pluck('total_vendido');

        // Productos con bajo stock (menos de 10 unidades)


        $lowStockProducts = Producto::withSum('lotes as total_cantidad', 'cantidad')
            ->where('sucursal_id', $sucursal_id)
            ->having('total_cantidad', '<', 10)
            ->orderBy('total_cantidad', 'asc')
            ->get();




        return view('home', compact(
            'sucursal',
            'total_productos',
            'total_compras',
            'total_clientes',
            'total_ventas',
            'compras',
            'labels',
            'data',
            'topProducts',
            'lowStockProducts',
            'breadcrumb',
            'tiempo_cambio_contraseña'
        ));
    }
}