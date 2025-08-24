<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\User;
use App\Models\Sucursal;

use App\Models\Compra;
use App\Models\Lote;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\DetalleVenta; // Asegúrate de importar el modelo DetalleVenta
use Illuminate\Support\Facades\DB; // Necesario para las consultas
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Catalogo', 'url' => route('admin.catalogo.index')],


        ];

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
            'breadcrumb' => $breadcrumb
        ]);
    }
    public function show($id)
    {
        $producto = Producto::with(['categoria', 'lotes'])->findOrFail($id);
        $categorias = Categoria::all();

        // Obtener precio mínimo y stock total desde los lotes
        $producto->precio_minimo = $producto->lotes->min('precio_venta') ?? 0;
        $producto->stock = $producto->lotes->sum('cantidad');

        return view('admin.catalogo.show', compact('producto', 'categorias'));
    }


    public function ver(Categoria $categoria)
    {
        $productosPaginados = $categoria->productos()->with('lotes')->paginate(12);

        // Calcular precio mínimo y stock total por producto
        foreach ($productosPaginados as $producto) {
            $producto->precio_minimo = $producto->lotes->min('precio_venta') ?? 0;
            $producto->stock = $producto->lotes->sum('cantidad');
        }

        return view('admin.catalogo.ver', [
            'productos' => $productosPaginados,
            'categoria' => $categoria,
            'categorias' => Categoria::all()
        ]);
    }


    // En tu componente Livewire
    public function getCategoryIcon($categoryName)
    {
        $icons = [
            'Medicamentos' => 'fas fa-pills',
            'Cuidado Personal' => 'fas fa-spa',
            'Bebés' => 'fas fa-baby',
            'Vitaminas' => 'fas fa-apple-alt',
            'Dermocosmética' => 'fas fa-leaf',
            'Primeros Auxilios' => 'fas fa-first-aid',
            'Salud Sexual' => 'fas fa-heart',
            'Adultos Mayores' => 'fas fa-wheelchair'
        ];

        return $icons[$categoryName] ?? 'fas fa-tag';
    }


    // En tu controlador (CatalogoController.php)
    public function buscar(Request $request)
    {
        $request->validate(['search' => 'required|string|min:2']);

        $termino = $request->input('search');

        $productos = Producto::with('categoria')
            ->withMin('lotes as precio_minimo', 'precio_venta') // Precio más bajo desde Lotes
            ->withSum('lotes as total_cantidad', 'cantidad')    // Stock total desde Lotes (opcional)
            ->where('nombre', 'LIKE', "%{$termino}%")
            ->orWhere('descripcion', 'LIKE', "%{$termino}%")
            ->orWhereHas('categoria', function ($query) use ($termino) {
                $query->where('nombre', 'LIKE', "%{$termino}%");
            })
            ->paginate(12);

        $categorias = Categoria::all();

        return view('admin.catalogo.buscar', [
            'productos' => $productos,
            'terminoBusqueda' => $termino,
            'categorias' => $categorias
        ]);
    }




    // Ejemplo de controlador
// En tu controlador
// En tu controlador Laravel
    public function search(Request $request)
    {
        $query = $request->input('query', '');

        $results = Producto::with('lotes') // Asegúrate que la relación esté definida en el modelo Producto
            ->where('nombre', 'like', "%$query%")
            ->take(10)
            ->get()
            ->map(function ($item) {
                // Obtener el precio más bajo desde los lotes
                $precio_venta = $item->lotes->min('precio_venta') ?? 0;

                return [
                    'nombre' => $item->nombre,
                    'url' => route('admin.catalogo.show', $item->id), // Asegúrate que esta sea la ruta correcta
                    'imagen' => $item->imagen ? asset('storage/' . $item->imagen) : asset('img/default-product.png'),
                    'precio' => 'Bs ' . number_format($precio_venta, 2) // Ahora se incluye el precio correcto
                ];
            });

        return response()->json($results);
    }


}