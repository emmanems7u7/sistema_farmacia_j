<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class PageController extends Controller
{
    public function pagina_inicial(Request $request)
    {

        $search = $request->input('search');
        $categoriaId = $request->input('categoria');
        $sort = $request->input('sort', 'newest'); // Valor por defecto 'newest'

        // Consulta de productos más vendidos (se mantiene igual)
        $topProductos = Producto::withSum('lotes as total_cantidad', 'cantidad')
            ->join('detalle_ventas', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->select(
                'productos.id',
                'productos.nombre',
                'productos.imagen',
                DB::raw('SUM(detalle_ventas.cantidad) as total_vendido')
            )
            ->groupBy('productos.id', 'productos.nombre', 'productos.imagen')
            ->orderByDesc('total_vendido')
            ->take(6)
            ->get();


        foreach ($topProductos as $producto) {
            $productoLotes = \App\Models\Lote::where('producto_id', $producto->id)->get();
            $producto->precio_minimo = $productoLotes->min('precio_venta') ?? 0;
            $producto->stock = $productoLotes->sum('cantidad');
        }

        // Consulta base
        $query = Producto::with('categoria');

        // Aplicar filtros existentes
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                    ->orWhere('codigo', 'LIKE', "%{$search}%")
                    ->orWhereHas('categoria', function ($q) use ($search) {
                        $q->where('nombre', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($categoriaId) {
            $query->where('categoria_id', $categoriaId);
        }

        // Lógica de ordenamiento MEJORADA
        $query = Producto::with('categoria')->withSum('lotes as total_cantidad', 'cantidad')->withMin('lotes as precio_minimo', 'precio_venta');

        switch ($sort) {
            case 'price_asc':
                $query->orderBy('precio_minimo'); // de los lotes
                break;

            case 'price_desc':
                $query->orderByDesc('precio_minimo'); // de los lotes
                break;

            case 'popular':
                $query->select('productos.*')
                    ->leftJoin('detalle_ventas', 'productos.id', '=', 'detalle_ventas.producto_id')
                    ->selectRaw('productos.*, SUM(IFNULL(detalle_ventas.cantidad, 0)) as total_vendido')
                    ->groupBy('productos.id')
                    ->orderByDesc('total_vendido');
                break;

            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
        }


        $productos = $query->get();

        return view('admin.catalogo.index', [
            'productos' => $productos,
            'categorias' => Categoria::all(),
            'searchTerm' => $search,
            'topProductos' => $topProductos,
            'currentSort' => $sort,

        ]);
    }


    /**
     * Display all the static pages when authenticated
     *
     * @param string $page
     * @return \Illuminate\View\View
     */
    public function index(string $page)
    {
        if (view()->exists("pages.{$page}")) {
            return view("pages.{$page}");
        }

        return abort(404);
    }

    public function vr()
    {
        return view("pages.virtual-reality");
    }

    public function rtl()
    {
        return view("pages.rtl");
    }

    public function profile()
    {
        return view("pages.profile-static");
    }

    public function signin()
    {
        return view("pages.sign-in-static");
    }

    public function signup()
    {
        return view("pages.sign-up-static");
    }
}
