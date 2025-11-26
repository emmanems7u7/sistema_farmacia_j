<?php

namespace App\Http\Controllers;
use NumberToWords\NumberToWords;
use App\Models\Caja;
use NumberFormatter;

use App\Models\Lote;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Sucursal;
use App\Models\Producto;

use App\Models\MovimientoCaja;
use Illuminate\Http\Request;
use App\Models\TmpVenta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;


use Illuminate\Support\Facades\Storage;


use Maatwebsite\Excel\Facades\Excel;

use Carbon\Carbon;

use DatePeriod; 
use DateInterval; 
use DateTime; 

use Symfony\Component\HttpFoundation\BinaryFileResponse;
class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Ventas', 'url' => route('admin.ventas.index')],
        ];
        $cajaAbierto = Caja::whereNull('fecha_cierre')->first();
        $ventas = Venta::with('detallesventa', 'cliente')
            ->orderBy('fecha', 'desc')  
            ->get();
        return view('admin.ventas.index', compact('breadcrumb', 'ventas', 'cajaAbierto'));


    }


    public function create()
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Ventas', 'url' => route('admin.ventas.index')],
            ['name' => 'Crear Venta', 'url' => route('admin.ventas.index')],

        ];
        $productos = Producto::where('sucursal_id', Auth::user()->sucursal_id)->get();
        //   $proveedores = Proveedor::where('sucursal_id', Auth::user()->sucursal_id)->get();
        $clientes = cliente::where('sucursal_id', Auth::user()->sucursal_id)->get();
        $session_id = session()->getId();
        $tmp_ventas = TmpVenta::where('session_id', $session_id)->get();
        return view('admin.ventas.create', compact('breadcrumb', 'productos', 'clientes', 'tmp_ventas'));
    }



    public function cliente_store(Request $request)
    {
        $validate = $request->validate([
            'nombre_cliente' => 'required',
            'nit_ci' => 'nullable',
            'celular' => 'nullable',
            'email' => 'nullable',
        ]);
        // Crear un nuevo cliente
        $cliente = new Cliente();
        $cliente->nombre_cliente = $request->nombre_cliente;
        $cliente->nit_ci = $request->nit_ci;
        $cliente->celular = $request->celular;
        $cliente->email = $request->email;
        $cliente->sucursal_id = Auth::user()->sucursal_id;
        $cliente->save();
        return response()->json(['success' => 'cliente registrado']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // Validación de los datos de entrada
    $request->validate([
        'fecha' => 'required',
        'precio_total' => 'required',
    ]);

    // Iniciar transacción para asegurar integridad de datos
    DB::beginTransaction();

    try {
        // Crear la venta principal
        $ventas = new Venta();
        $ventas->fecha = $request->fecha;
        $ventas->precio_total = $request->precio_total;
        $ventas->sucursal_id = Auth::user()->sucursal_id;
        $ventas->cliente_id = $request->cliente_id;
        $ventas->save();

        $session_id = session()->getId();

        // Registrar en la caja 
        $caja_id = Caja::whereNull('fecha_cierre')->first();
        $movimiento = new MovimientoCaja();
        $movimiento->tipo = "INGRESO";
        $movimiento->monto = $request->precio_total;
        $movimiento->descripcion = "venta de productos";
        $movimiento->fecha_movimiento = $request->fecha_movimiento ?? now();
        $movimiento->caja_id = $caja_id->id;
        $movimiento->save();

        // Procesar productos temporales
        $tmp_ventas = TmpVenta::where('session_id', $session_id)->get();

        foreach ($tmp_ventas as $tmp_venta) {
            // PEPS para descontar de múltiples lotes
            $cantidad_restante = $tmp_venta->cantidad;

            // Obtener lotes ordenados por fecha de ingreso (más antiguos primero)
            $lotes = Lote::where('producto_id', $tmp_venta->producto_id)
                ->where('cantidad', '>', 0)
                ->orderBy('fecha_ingreso', 'asc')
                ->get();

            // Crear un detalle de venta POR CADA LOTE utilizado
            foreach ($lotes as $lote) {
                if ($cantidad_restante <= 0)
                    break;

                $cantidad_a_descontar = min($lote->cantidad, $cantidad_restante);

                // CREAR DETALLE VENTA POR CADA LOTE
                $detalle_venta = new DetalleVenta();
                $detalle_venta->cantidad = $cantidad_a_descontar;
                $detalle_venta->venta_id = $ventas->id;
                $detalle_venta->producto_id = $tmp_venta->producto_id;
                $detalle_venta->lote_id = $lote->id; 
                $detalle_venta->save();

                // Descontar del lote
                $lote->cantidad -= $cantidad_a_descontar;
                $lote->save();

                $cantidad_restante -= $cantidad_a_descontar;
            }

            // Validar si hay suficiente stock
            if ($cantidad_restante > 0) {
                throw new \Exception("No hay suficiente stock para el producto: " . $tmp_venta->producto->nombre);
            }
        }

        // Eliminar temporales
        TmpVenta::where('session_id', $session_id)->delete();

        DB::commit();

      //  session()->flash('metodo_pago', $request->metodo_pago);
       // session()->flash('monto_recibido', $request->monto_recibido);
       // session()->flash('cambio', $request->cambio);

        return redirect()->route('admin.ventas.index')
            ->with('status', 'Se registró la venta correctamente');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('status', 'Error al registrar la venta: ' . $e->getMessage());
    }
}


public function verificarStock(Request $request)
{
    try {
        $codigo = $request->codigo;
        $cantidad_solicitada = $request->cantidad;

        // Buscar el producto
        $producto = Producto::where('codigo', $codigo)->first();

        if (!$producto) {
            return response()->json([
                'error' => 'Producto no encontrado'
            ], 404);
        }

        // Calcular stock total disponible
        $stock_disponible = $producto->lotes()
            ->where('cantidad', '>', 0)
            ->sum('cantidad');

        $faltante = max(0, $cantidad_solicitada - $stock_disponible);
        $stock_suficiente = $stock_disponible >= $cantidad_solicitada;

        return response()->json([
            'stock_suficiente' => $stock_suficiente,
            'stock_disponible' => $stock_disponible,
            'faltante' => $faltante,
            'producto_nombre' => $producto->nombre,
            'cantidad_solicitada' => $cantidad_solicitada
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error al verificar stock: ' . $e->getMessage()
        ], 500);
    }
}



public function updateTmp(Request $request, $id)
{
    try {
        $tmp_venta = TmpVenta::findOrFail($id);
        
        $request->validate([
            'cantidad' => 'required|integer|min:1'
        ]);

        $tmp_venta->cantidad = $request->cantidad;
        $tmp_venta->save();

        return response()->json([
            'success' => true,
            'message' => 'Cantidad actualizada correctamente'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar: ' . $e->getMessage()
        ], 500);
    }
}

public function reporteDia(Request $request)
{
    try {
        $tipo = $request->input('tipo', 'dia');

        $sucursal = Sucursal::find(Auth::user()->sucursal_id);

        $query = Venta::with(['detallesVenta.producto', 'detallesVenta.lote', 'cliente'])
            ->where('sucursal_id', Auth::user()->sucursal_id);

        $hoy = now()->format("Y-m-d");

        switch ($tipo) {

            case 'dia':
                $inicio = $request->input('fecha', $hoy);
                $fin = $inicio;
                $query->whereDate('fecha', $inicio);
                $titulo = "REPORTE DE VENTAS DEL DÍA";
                break;

            case 'semana':
                $inicio = now()->startOfWeek()->format("Y-m-d");
                $fin = now()->endOfWeek()->format("Y-m-d");
                $query->whereBetween('fecha', [$inicio, $fin]);
                $titulo = "REPORTE SEMANAL";
                break;

            case 'mes':
                $inicio = now()->startOfMonth()->format("Y-m-d");
                $fin = now()->endOfMonth()->format("Y-m-d");
                $query->whereBetween('fecha', [$inicio, $fin]);
                $titulo = "REPORTE MENSUAL";
                break;

            case 'anio':
                $inicio = now()->startOfYear()->format("Y-m-d");
                $fin = now()->endOfYear()->format("Y-m-d");
                $query->whereBetween('fecha', [$inicio, $fin]);
                $titulo = "REPORTE ANUAL";
                break;

            case 'rango':
                $inicio = $request->fecha_inicio;
                $fin = $request->fecha_fin;

                if (!$inicio || !$fin) {
                    throw new \Exception("Debe seleccionar un rango de fechas válido");
                }

                $query->whereBetween('fecha', [$inicio, $fin]);
                $titulo = "REPORTE POR RANGO PERSONALIZADO";
                break;

            default:
                throw new \Exception("Tipo de reporte inválido");
        }

        $ventas = $query->orderBy('fecha', 'desc')->get();

        if ($ventas->count() === 0) {
            throw new \Exception("No se encontraron ventas en este periodo");
        }

        // Para compatibilidad con la vista antigua que usa $fecha
        // asignamos $fecha al inicio (si es día será ese día; en rangos será el inicio)
        $fecha = $inicio ?? $hoy;

        $data = [
            'ventas' => $ventas,
            'titulo' => $titulo,
            'inicio' => $inicio,
            'fin' => $fin,
            'fecha' => $fecha, // <-- variable agregada para evitar "Undefined variable $fecha"
            'sucursal' => $sucursal,
            'fecha_generacion' => now()->format('d/m/Y H:i:s'),
            'totalVentas' => $ventas->count(),
            'totalIngresos' => $ventas->sum('precio_total'),
            'totalProductos' => $ventas->sum(fn($v) => $v->detallesVenta->sum('cantidad'))
        ];

        // Preferimos la vista general si existe, sino usamos la vieja reporte_dia para compatibilidad
        $vista = view()->exists('admin.ventas.reporte_general')
                 ? 'admin.ventas.reporte_general'
                 : 'admin.ventas.reporte_dia';

        $pdf = PDF::loadView($vista, $data)
                 ->setPaper('a4', 'portrait');

        return $pdf->download("reporte_" . $tipo . ".pdf");

    } catch (\Exception $e) {

        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
        ], 500);
    }
}


    public function pdf($id)
{
    $id_sucursal = Auth::user()->sucursal_id;
    $sucursal = Sucursal::findOrFail($id_sucursal);

    $venta = Venta::with(['detallesVenta.producto', 'detallesVenta.lote', 'cliente'])->findOrFail($id);

    // Calcular el total sumando los subtotales usando precio del lote si existe
    $total = $venta->detallesVenta->sum(function ($detalle) {
        $precio = $detalle->lote->precio_venta ?? $detalle->producto->precio_venta;
        return $detalle->cantidad * $precio;
    });

    // Generar el literal
    $literal = $this->numerosALetrasConDecimales($total);

    // Enviar a la vista del PDF
    return PDF::loadView('admin.ventas.pdf', [
        'sucursal' => $sucursal,
        'venta' => $venta,
        'literal' => $literal, 
        'total' => $total      
    ])->setPaper([0, 0, 250.77, 600], 'portrait')->stream();
}




private function numerosALetrasConDecimales($numero)
{
    $unidades = [
        '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve', 'diez',
        'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve', 'veinte'
    ];
    $decenas = ['', '', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'];
    $centenas = ['', 'cien', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'];

    $numero = round($numero, 2);
    $entero = floor($numero);
    $decimal = (int)round(($numero - $entero) * 100);

    $convertir = function ($n) use ($unidades, $decenas, $centenas, &$convertir) {
        if ($n == 0) return 'cero';
        elseif ($n < 21) return $unidades[$n];
        elseif ($n < 100) {
            $d = intdiv($n, 10);
            $u = $n % 10;
            return $decenas[$d] . ($u ? ' y ' . $unidades[$u] : '');
        } elseif ($n < 1000) {
            $c = intdiv($n, 100);
            $r = $n % 100;
            $texto = $centenas[$c];
            if ($c == 1 && $r > 0) $texto = 'ciento';
            return trim($texto . ($r ? ' ' . $convertir($r) : ''));
        } elseif ($n < 1000000) {
            $m = intdiv($n, 1000);
            $r = $n % 1000;
            $miles = $m == 1 ? 'mil' : $convertir($m) . ' mil';
            return trim($miles . ' ' . ($r ? $convertir($r) : ''));
        } elseif ($n < 1000000000) {
            $millones = intdiv($n, 1000000);
            $r = $n % 1000000;
            $textoMillones = $millones == 1 ? 'un millón' : $convertir($millones) . ' millones';
            return trim($textoMillones . ' ' . ($r ? $convertir($r) : ''));
        } else {
            return (string)$n;
        }
    };

    $textoEntero = $convertir($entero) . ' boliviano' . ($entero != 1 ? 's' : '');
    $textoDecimal = $decimal == 0 ? 'exactos' : $convertir($decimal) . ' centavo' . ($decimal != 1 ? 's' : '');

    return ucfirst(trim(($numero < 0 ? 'menos ' : '') . "$textoEntero con $textoDecimal"));
}


    public function show($id)
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Ventas', 'url' => route('admin.ventas.index')],
            ['name' => 'Ver Venta', 'url' => route('admin.ventas.index')],

        ];
      //  $venta = Venta::with('detallesVenta', 'cliente')->findOrfail($id);
    
        $venta = Venta::with(['detallesVenta.producto', 'detallesVenta.lote', 'cliente'])->findOrfail($id);
        return view('admin.ventas.show', compact('breadcrumb', 'venta'));
        

    }

    public function edit($id)
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Ventas', 'url' => route('admin.ventas.index')],
            ['name' => 'Editar Venta', 'url' => route('admin.ventas.index')],

        ];
        $productos = Producto::all();
        $clientes = Cliente::all();
        $venta = Venta::with('detallesVenta', 'cliente')->findOrfail($id);
        return view('admin.ventas.edit', compact('breadcrumb', 'venta', 'productos', 'clientes'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        // Validación de los datos de entrada
        $request->validate([
            'fecha' => 'required',

            'precio_total' => 'required', //
        ]);

        // Crear un nuevo laboratorio
        $venta = Venta::find($id);
        $venta->fecha = $request->fecha;

        $venta->precio_total = $request->precio_total;

        $venta->sucursal_id = Auth::user()->sucursal_id;
        $venta->cliente_id = $request->cliente_id;
        $venta->save();

        $session_id = session()->getId();




        return redirect()->route('admin.ventas.index')
            ->with('status', 'Venta actualizada correctamente');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $venta = Venta::with(['detallesVenta.producto.lotes'])->findOrFail($id);

       
            foreach ($venta->detallesVenta as $detalle) {
                $lote = $detalle->producto->lotes->first();
                if ($lote) {
                    $lote->increment('cantidad', $detalle->cantidad);
                }
            }

           
            $venta->delete();

            DB::commit();

            return redirect()->route('admin.ventas.index')
                ->with('status', 'Venta anulada y stock restaurado');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error eliminando venta {$id}: " . $e->getMessage());

            return redirect()->back()
                ->with('status', 'No se pudo anular la venta: ' . $e->getMessage());
        }
    }
    //reportes

    public function reporte($tipo, Request $request)
    {
        // Validar el tipo de reporte
        if (!in_array($tipo, ['pdf', 'excel', 'csv'])) {
            abort(400, 'Tipo de reporte no válido');
        }

        // Obtener filtros
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');
        $cliente_id = $request->input('cliente_id');

        $query = Venta::with(['detallesVenta', 'cliente'])
            ->where('sucursal_id', Auth::user()->sucursal_id);

        if ($fecha_inicio && $fecha_fin) {
            $query->whereBetween('fecha', [$fecha_inicio, $fecha_fin]);
        }

        if ($cliente_id) {
            $query->where('cliente_id', $cliente_id);
        }

        $ventas = $query->get();

        // Verificar si hay datos
        if ($ventas->isEmpty()) {
            return back()->with('error', 'No hay ventas con los filtros seleccionados');
        }

        switch ($tipo) {
            case 'pdf':
                return $this->generarPDF($ventas);
            case 'excel':
                return $this->generarExcel($ventas);
            case 'csv':
                return $this->generarCSV($ventas);
        }
    }

    private function generarPDF($ventas)
    {
        $pdf = PDF::loadView('admin.ventas.reporte', [
            'ventas' => $ventas,
            'fecha_generacion' => now()->format('d/m/Y H:i:s')
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reporte_ventas_' . now()->format('YmdHis') . '.pdf');


    }

    private function generarExcel($ventas)
    {
        $data = $ventas->map(function ($venta) {
            return [
                'Fecha' => $venta->fecha,
                'N° Factura' => $venta->numero_factura ?? 'N/A',
                'Cliente' => $venta->cliente->nombre_cliente ?? 'Sin cliente',
                'CI/NIT' => $venta->cliente->nit_ci ?? 'N/A',
                'Total (Bs)' => number_format($venta->precio_total, 2),
                'Productos' => $venta->detallesVenta->count(),
                'Cantidad Total' => $venta->detallesVenta->sum('cantidad'),
                'Método Pago' => $venta->metodo_pago ?? 'No especificado'
            ];
        });

        return Excel::download(
            new class ($data) implements
                \Maatwebsite\Excel\Concerns\FromCollection,
                \Maatwebsite\Excel\Concerns\WithHeadings,
                \Maatwebsite\Excel\Concerns\WithStyles,
                \Maatwebsite\Excel\Concerns\ShouldAutoSize,
                \Maatwebsite\Excel\Concerns\WithColumnWidths {

            private $data;

            public function __construct($data)
            {
                $this->data = collect($data);
            }

            public function collection()
            {
                return $this->data;
            }

            public function headings(): array
            {
                return [
                    'Fecha',
                    'N° Factura',
                    'Cliente',
                    'CI/NIT',
                    'Total (Bs)',
                    'Productos',
                    'Cantidad Total',
                    'Método Pago'
                ];
            }

            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
            {
                return [
                    // Estilo encabezados
                    1 => [
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF'],
                            'size' => 12
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '3498DB'] // Azul
                        ],
                        'alignment' => [
                            'horizontal' => 'center',
                            'vertical' => 'center'
                        ]
                    ],
                    // Estilo cuerpo
                    'A2:H' . $sheet->getHighestRow() => [
                        'alignment' => [
                            'vertical' => 'center',
                            'horizontal' => 'center'
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['rgb' => 'EEEEEE']
                            ]
                        ]
                    ],
                    
                    'C2:C' . $sheet->getHighestRow() => [
                        'alignment' => ['horizontal' => 'left']
                    ],
                   
                    'E2:E' . $sheet->getHighestRow() => [
                        'numberFormat' => [
                            'formatCode' => '#,##0.00'
                        ]
                    ]
                ];
            }

            public function columnWidths(): array
            {
                return [
                    'A' => 12,  // Fecha
                    'B' => 15,   // N° Factura
                    'C' => 30,   // Cliente
                    'D' => 15,   // CI/NIT
                    'E' => 15,   // Total
                    'F' => 12,   // Productos
                    'G' => 15,   // Cantidad
                    'H' => 20    // Método Pago
                ];
            }
            },
            'reporte_ventas_' . now()->format('YmdHis') . '.xlsx'
        );
    }



}