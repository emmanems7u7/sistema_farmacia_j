<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\Validator;
use App\Models\Caja;
use App\Models\Venta;
use App\Models\MovimientoCaja;
use App\Models\TmpCompra;
use NumberToWords\NumberToWords;
use App\Models\Lote;
use NumberFormatter;
use App\Models\Proveedor;
use App\Models\Laboratorio;

use Maatwebsite\Excel\Facades\Excel;

use Carbon\Carbon;

use DatePeriod; // Importación añadida
use DateInterval; // Importación añadida
use DateTime; // Importación añadida
// Asegúrate de tener este modelo para acceder a los datos de ingresos
use PDF;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


use Illuminate\Support\Facades\Auth; // Importar Auth
use Illuminate\Support\Facades\DB; // ¡Este es el import que faltaba!
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InventarioController extends Controller
{
    public function index(Request $request)
    {

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Inventario', 'url' => route('admin.inventario.index')],
        ];
        // Obtener sucursal seleccionada con nombre
        $sucursalId = $request->get('sucursal', 0);
        $sucursalNombre = '';

        // Obtener todas las sucursales para el dropdown
        $sucursales = Sucursal::all();

        // Obtener nombre de sucursal si está seleccionada
        if ($sucursalId > 0) {
            $sucursal = Sucursal::find($sucursalId);
            $sucursalNombre = $sucursal ? $sucursal->nombre : '';
        }

        // Consultas básicas
        $queryCompras = Compra::query();
        $queryVentas = Venta::query();
        $queryProductos = Producto::query();
        $queryLotes = Lote::query();

        // Filtro por sucursal
        if ($sucursalId > 0) {
            $queryCompras->where('sucursal_id', $sucursalId);
            $queryVentas->where('sucursal_id', $sucursalId);
            $queryProductos->where('sucursal_id', $sucursalId);
            $queryLotes->where('sucursal_id', $sucursalId);
        }

        // ===== DATOS PARA EL CARD DE COMPRAS MENSUALES =====
        // Manejo del mes seleccionado (viene del request o usa el actual)
        $mesSeleccionado = $request->has('month')
            ? Carbon::parse($request->month)
            : now();

        // Inicializar valores por defecto para compras
        $totalComprasMes = 0;
        $cantidadCompras = 0;
        $mesTieneCompras = false;

        // Obtener meses disponibles con compras
        $mesesDisponiblesCompras = $queryCompras->clone()
            ->selectRaw('YEAR(fecha) as year, MONTH(fecha) as month')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                return Carbon::create($item->year, $item->month, 1);
            });

        // Verificar si el mes seleccionado tiene compras
        $mesTieneCompras = $mesesDisponiblesCompras->contains(function ($mes) use ($mesSeleccionado) {
            return $mes->format('Y-m') == $mesSeleccionado->format('Y-m');
        });

        // Solo calcular si hay compras en el mes seleccionado
        if ($mesTieneCompras) {
            $totalComprasMes = $queryCompras->clone()
                ->whereYear('fecha', $mesSeleccionado->year)
                ->whereMonth('fecha', $mesSeleccionado->month)
                ->sum('precio_total');

            $cantidadCompras = $queryCompras->clone()
                ->whereYear('fecha', $mesSeleccionado->year)
                ->whereMonth('fecha', $mesSeleccionado->month)
                ->count();
        }
        // ===== FIN DE SECCIÓN DE COMPRAS =====

        // ===== DATOS PARA EL CARD DE VENTAS MENSUALES =====
        // Inicializar valores por defecto para ventas
        $totalVentasMes = 0;
        $cantidadVentas = 0;
        $mesTieneVentas = false;

        // Obtener meses disponibles con ventas
        $mesesDisponiblesVentas = $queryVentas->clone()
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                return Carbon::create($item->year, $item->month, 1);
            });

        // Verificar si el mes seleccionado tiene ventas
        $mesTieneVentas = $mesesDisponiblesVentas->contains(function ($mes) use ($mesSeleccionado) {
            return $mes->format('Y-m') == $mesSeleccionado->format('Y-m');
        });

        // Solo calcular si hay ventas en el mes seleccionado
        if ($mesTieneVentas) {
            $totalVentasMes = $queryVentas->clone()
                ->whereYear('created_at', $mesSeleccionado->year)
                ->whereMonth('created_at', $mesSeleccionado->month)
                ->sum('precio_total');

            $cantidadVentas = $queryVentas->clone()
                ->whereYear('created_at', $mesSeleccionado->year)
                ->whereMonth('created_at', $mesSeleccionado->month)
                ->count();
        }
        // ===== FIN DE SECCIÓN DE VENTAS =====

        // Métricas (solo conteos)
        $totalProductos = $queryProductos->count();
        $ventasTotales = $queryVentas->whereMonth('created_at', now()->month)->count();

        $productosBajoStock = $queryProductos->whereHas('lotes', function ($q) {
            $q->where('cantidad', '<', DB::raw('productos.stock_minimo'));
        })->count();

        $productosPorVencer = $queryLotes->whereBetween('fecha_vencimiento', [now(), now()->addDays(30)])
            ->where('cantidad', '>', 0)
            ->count();

        // Combinar meses disponibles de compras y ventas para el filtro
        $mesesDisponibles = $mesesDisponiblesCompras->merge($mesesDisponiblesVentas)
            ->unique()
            ->sortDesc();

        return view('admin.inventario.index', compact(
            'sucursales',
            'sucursalId',
            'sucursalNombre', // Añadido para el card de productos
            'totalProductos',
            'ventasTotales',
            'productosBajoStock',
            'productosPorVencer',
            // Variables para el card de compras
            'mesSeleccionado',
            'mesesDisponibles',
            'totalComprasMes',
            'cantidadCompras',
            'mesTieneCompras',
            // Variables para el card de ventas
            'totalVentasMes',
            'cantidadVentas',
            'mesTieneVentas',
            'breadcrumb'
        ));
    }


    public function bajoStock(Request $request)
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Inventario', 'url' => route('admin.inventario.index')],
            ['name' => 'Productos Bajo Stock', 'url' => route('admin.inventario.index')],

        ];

        $sucursalId = $request->query('sucursal', 0);
        $alerta = $request->query('alerta');

        // Consulta base con eager loading y suma de lotes
        $query = Producto::with(['lotes', 'sucursal', 'categoria'])
            ->withSum('lotes', 'cantidad')
            ->having('lotes_sum_cantidad', '<=', 15) // Solo productos con ≤15 unidades
            ->orderBy('lotes_sum_cantidad'); // Ordenar de menor a mayor stock

        // Filtro por sucursal
        if ($sucursalId > 0) {
            $query->where('sucursal_id', $sucursalId);
        }

        // Filtro por nivel de alerta
        if ($alerta) {
            switch ($alerta) {
                case 'critico':
                    $query->having('lotes_sum_cantidad', '<=', 5);
                    break;
                case 'advertencia':
                    $query->having('lotes_sum_cantidad', '>', 5)
                        ->having('lotes_sum_cantidad', '<=', 10);
                    break;
                case 'precaucion':
                    $query->having('lotes_sum_cantidad', '>', 10)
                        ->having('lotes_sum_cantidad', '<=', 15);
                    break;
            }
        }

        // Paginación y transformación de resultados
        $productos = $query->paginate(15)->withQueryString();

        // Clasificar cada producto según su nivel de stock
        $productos->getCollection()->transform(function ($producto) {
            $stockActual = $producto->lotes_sum_cantidad;
            $producto->diferencia = $stockActual - $producto->stock_minimo;

            // Definir niveles de alerta
            if ($stockActual <= 0) {
                $producto->nivel_alerta = 'danger';
                $producto->icono_alerta = 'fa-times-circle';
                $producto->texto_alerta = 'SIN STOCK';
            } elseif ($stockActual <= 5) {
                $producto->nivel_alerta = 'danger';
                $producto->icono_alerta = 'fa-fire';
                $producto->texto_alerta = 'CRÍTICO';
            } elseif ($stockActual <= 10) {
                $producto->nivel_alerta = 'warning';
                $producto->icono_alerta = 'fa-exclamation-triangle';
                $producto->texto_alerta = 'ADVERTENCIA';
            } elseif ($stockActual <= 15) {
                $producto->nivel_alerta = 'info';
                $producto->icono_alerta = 'fa-info-circle';
                $producto->texto_alerta = 'PRECAUCIÓN';
            } else {
                $producto->nivel_alerta = 'success';
                $producto->icono_alerta = 'fa-check-circle';
                $producto->texto_alerta = 'NORMAL';
            }

            return $producto;
        });

        return view('admin.inventario.bajo_stock', [
            'productos' => $productos,
            'sucursalId' => $sucursalId,
            'sucursales' => Sucursal::all(),
            'breadcrumb' => $breadcrumb
        ]);
    }

    public function productosPorVencer(Request $request)
    {

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Inventario', 'url' => route('admin.inventario.index')],
            ['name' => 'Productos por Vencer', 'url' => route('admin.inventario.index')],

        ];

        // Obtener y validar parámetros
        $sucursalId = $request->query('sucursal');
        $dias = $request->query('dias', 30);
        $mostrarVencidos = $request->query('vencidos', false); // Nuevo parámetro

        $validator = Validator::make($request->all(), [
            'sucursal' => 'nullable|integer|exists:sucursals,id',
            'dias' => 'nullable|integer|min:1|max:365',
            'vencidos' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            $sucursalId = null;
            $dias = 30;
            $mostrarVencidos = false;
        }

        // Consulta optimizada
        $query = Producto::select([
            'productos.id',
            'productos.codigo',
            'productos.nombre',
            'lotes.numero_lote',
            'lotes.cantidad as cantidad_lote',
            'lotes.fecha_vencimiento',
            DB::raw('(CASE WHEN lotes.fecha_vencimiento < NOW() THEN 1 ELSE 0 END) as vencido') // Agregar campo vencido
        ])
            ->join('lotes', 'productos.id', '=', 'lotes.producto_id');

        // Filtro por sucursal
        if ($sucursalId) {
            $query->where('lotes.sucursal_id', $sucursalId);
        }

        // Filtro por fecha
        if ($mostrarVencidos) {
            // Mostrar solo productos vencidos
            $query->whereDate('lotes.fecha_vencimiento', '<', now());
        } else {
            // Mostrar productos por vencer (dentro del rango de días)
            $query->whereBetween('lotes.fecha_vencimiento', [
                now(),
                now()->addDays((int) $dias)
            ]);
        }

        $productos = $query->orderBy('vencido', 'desc') // Primero los vencidos
            ->orderBy('lotes.fecha_vencimiento', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.inventario.productos_porvencer', [
            'productos' => $productos,
            'sucursales' => Sucursal::all(),
            'sucursalId' => $sucursalId,
            'diasSeleccionados' => $dias,
            'mostrarVencidos' => $mostrarVencidos,
            'breadcrumb' => $breadcrumb
        ]);
    }


    public function comprasMensuales(Request $request)
    {
        // Obtener mes seleccionado o actual
        $mesSeleccionado = $request->month ? Carbon::parse($request->month) : Carbon::now();

        // Obtener meses disponibles con compras (simplificado)
        $mesesDisponibles = Compra::selectRaw('YEAR(fecha_compra) as year, MONTH(fecha_compra) as month')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                return Carbon::create($item->year, $item->month, 1);
            });

        // Calcular totales usando precio_total
        $totalComprasMes = Compra::whereYear('fecha_compra', $mesSeleccionado->year)
            ->whereMonth('fecha_compra', $mesSeleccionado->month)
            ->sum('precio_total');  // Cambiado de 'total' a 'precio_total'

        $cantidadCompras = Compra::whereYear('fecha_compra', $mesSeleccionado->year)
            ->whereMonth('fecha_compra', $mesSeleccionado->month)
            ->count();

        return view('tu.vista', compact(
            'mesSeleccionado',
            'mesesDisponibles',
            'totalComprasMes',
            'cantidadCompras'
        ));
    }


    public function imprimirCompras(Request $request)
    {
        // Validar y parsear fecha
        $request->validate([
            'month' => 'required|date_format:Y-m'
        ]);

        $mes = Carbon::parse($request->month);
        $sucursalId = $request->get('sucursal', 0);

        // Consulta principal con relaciones anidadas
        $query = Compra::with([
            'laboratorio',
            'detalles.producto.lotes' => function ($query) {
                $query->select('id', 'producto_id', 'precio_compra', 'numero_lote')
                    ->orderBy('created_at', 'desc'); // Obtener el lote más reciente
            },
            'detalles.producto' => function ($query) {
                $query->select('id', 'nombre'); // Solo los campos necesarios del producto
            }
        ])
            ->whereYear('fecha', $mes->year)
            ->whereMonth('fecha', $mes->month)
            ->orderBy('fecha', 'desc');

        // Filtro por sucursal si aplica
        if ($sucursalId > 0) {
            $query->where('sucursal_id', $sucursalId);
        }

        // Obtener compras y asignar lotes a cada detalle
        $compras = $query->get()->map(function ($compra) {
            $compra->detalles->each(function ($detalle) {
                // Asignar el primer lote del producto al detalle
                if ($detalle->producto && $detalle->producto->relationLoaded('lotes')) {
                    $detalle->lote = $detalle->producto->lotes->first();
                } else {
                    $detalle->lote = null;
                }
            });
            return $compra;
        });

        // Calcular total general
        $total = $compras->sum('precio_total');

        // Obtener información de sucursal
        $sucursalInfo = $sucursalId > 0
            ? Sucursal::findOrFail($sucursalId)
            : null;

        $sucursalNombre = $sucursalInfo ? $sucursalInfo->nombre : 'Todas las sucursales';
        $sucursalDireccion = $sucursalInfo ? $sucursalInfo->direccion : '';

        // Generar PDF
        $pdf = Pdf::loadView('admin.inventario.reporte_compras', compact(
            'compras',
            'total',
            'mes',
            'sucursalNombre',
            'sucursalDireccion'
        ));

        // Configuración del PDF
        $pdf->setPaper([0, 0, 612, 792], 'portrait');
        $pdf->setOption([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial'
        ]);

        return $pdf->stream('reporte-compras-' . $mes->format('m-Y') . '.pdf');
    }


    public function imprimirVentas(Request $request)
    {
        // Validar y parsear fecha
        $request->validate([
            'month' => 'required|date_format:Y-m'
        ]);

        $mes = Carbon::parse($request->month);
        $sucursalId = $request->get('sucursal', 0);

        // Consulta principal con relaciones anidadas
        $query = Venta::with([
            'cliente',
            'usuario',
            'detallesVenta.producto.lotes' => function ($query) {
                $query->select('id', 'producto_id', 'precio_venta')
                    ->orderBy('created_at', 'desc'); // Obtener el lote más reciente
            },
            'detallesVenta.producto' => function ($query) {
                $query->select('id', 'nombre', 'codigo');
            },
            'sucursal'
        ])
            ->whereYear('created_at', $mes->year)
            ->whereMonth('created_at', $mes->month);

        // Filtro por sucursal si aplica
        if ($sucursalId > 0) {
            $query->where('sucursal_id', $sucursalId);
        }

        // Obtener y procesar ventas
        $ventas = $query->orderBy('created_at', 'desc')->get()->map(function ($venta) {
            $venta->detallesVenta->each(function ($detalle) {
                // Asignar precio_venta del lote o usar precio_unitario como fallback
                $detalle->precio_final = $detalle->producto->lotes->first()->precio_venta ?? $detalle->precio_unitario;

                // Calcular subtotal considerando descuento
                $detalle->subtotal_calculado = ($detalle->precio_final * $detalle->cantidad) - ($detalle->descuento ?? 0);
            });
            return $venta;
        });

        // Calcular totales
        $totalVentas = $ventas->sum('precio_total');
        $totalProductosVendidos = $ventas->sum(function ($venta) {
            return $venta->detallesVenta->sum('cantidad');
        });

        // Obtener información de sucursal
        $sucursalInfo = $sucursalId > 0
            ? Sucursal::find($sucursalId)
            : null;

        $sucursalNombre = $sucursalInfo ? $sucursalInfo->nombre : 'Todas las sucursales';
        $sucursalDireccion = $sucursalInfo ? $sucursalInfo->direccion : '';

        // Datos para la vista
        $data = [
            'ventas' => $ventas,
            'totalVentas' => $totalVentas,
            'totalProductosVendidos' => $totalProductosVendidos,
            'mes' => $mes->translatedFormat('F Y'),
            'sucursalNombre' => $sucursalNombre,
            'sucursalDireccion' => $sucursalDireccion,
            'fechaGeneracion' => now()->format('d/m/Y H:i:s')
        ];

        // Generar PDF
        $pdf = Pdf::loadView('admin.inventario.reporte_ventas', $data);

        // Configuración del PDF
        $pdf->setPaper([0, 0, 612, 792], 'portrait');
        $pdf->setOption([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial'
        ]);

        return $pdf->stream('reporte-ventas-' . $mes->format('m-Y') . '.pdf');
    }

    public function reporteGeneral(Request $request)
    {

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Inventario', 'url' => route('admin.inventario.index')],
            ['name' => 'Reporte General', 'url' => route('admin.inventario.index')],

        ];
        $sucursalId = $request->get('sucursal', 0);
        $sucursalNombre = 'Todas las sucursales';

        if ($sucursalId > 0) {
            $sucursal = Sucursal::find($sucursalId);
            $sucursalNombre = $sucursal ? $sucursal->nombre : 'Sucursal seleccionada';
        }

        return view('admin.inventario.reportegeneral', [
            'sucursalId' => $sucursalId,
            'sucursalNombre' => $sucursalNombre,
            'sucursales' => Sucursal::all(),
            'tipoReporte' => 'pdf',
            'breadcrumb' => $breadcrumb
        ]);
    }
}





