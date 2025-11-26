<?php

namespace App\Http\Controllers;
use App\Models\Sucursal;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Caja;
use Illuminate\Support\Facades\Cache;
use App\Models\MovimientoCaja;
use App\Models\TmpCompra;
use NumberToWords\NumberToWords;
use App\Models\Lote;
use NumberFormatter;
use App\Models\Proveedor;
use App\Models\Laboratorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Maatwebsite\Excel\Facades\Excel;

use Carbon\Carbon;

use DatePeriod; 
use DateInterval; 
use DateTime; 
use PDF;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Compras', 'url' => route('admin.compras.index')],
        ];
        $cajaAbierto = Caja::whereNull('fecha_cierre')->first();
        $compras = Compra::with(['detalles', 'laboratorio'])->get();
        $sucursals = Sucursal::all();

        return view('admin.compras.index', compact('breadcrumb', 'compras', 'cajaAbierto', 'sucursals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {


        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Compras', 'url' => route('admin.compras.index')],
            ['name' => 'Crear Compra', 'url' => route('admin.compras.create')],

        ];
        $productos = Producto::where('sucursal_id', Auth::user()->sucursal_id)->get();
        //   $proveedores = Proveedor::where('sucursal_id', Auth::user()->sucursal_id)->get();
        $laboratorios = Laboratorio::all();
        $session_id = session()->getId();
        $tmp_compras = TmpCompra::where('session_id', $session_id)->get();
        $lotesPorProducto = \App\Models\Lote::latest('id')->get()->groupBy('producto_id');
        $productosConLotes = Producto::with('lotes')->get();

        return view('admin.compras.create', compact('breadcrumb', 'productos', 'laboratorios', 'tmp_compras', 'lotesPorProducto', 'productosConLotes'));

    }


  public function store(Request $request)
{
    
    $request->validate([
        'fecha' => 'required|date',
        'comprobante' => 'required|string|max:50',
        'precio_total' => 'required|numeric|min:0',
        // 'lotes' => 'required|array', // QUITADO
        // 'lotes.*' => 'required|exists:lotes,id' // QUITADO
    ]);

    $caja = Caja::whereNull('fecha_cierre')->firstOrFail();

    DB::beginTransaction();

    try {
        // Crear la compra
        $compra = Compra::create([
            'fecha' => $request->fecha,
            'comprobante' => $request->comprobante,
            'precio_total' => $request->precio_total,
            'sucursal_id' => Auth::user()->sucursal_id,
            'laboratorio_id' => $request->laboratorio_id
        ]);

        // Movimiento de caja
        MovimientoCaja::create([
            'tipo' => "EGRESO",
            'monto' => $request->precio_total,
            'descripcion' => "Compra de productos",
            'fecha_movimiento' => $request->fecha_movimiento ?? now(),
            'caja_id' => $caja->id
        ]);

        // Procesar productos temporales
        $session_id = session()->getId();
        $tmp_compras = TmpCompra::where('session_id', $session_id)->with('producto')->get();

        //  VERIFICAR QUE TODOS LOS PRODUCTOS TENGAN LOTE ASIGNADO
        foreach ($tmp_compras as $tmp_compra) {
            if (!$tmp_compra->lote_id) {
                throw new \Exception("El producto '{$tmp_compra->producto->nombre}' no tiene un lote asignado. Por favor, crea un lote para este producto.");
            }
        }

        foreach ($tmp_compras as $tmp_compra) {
            //  Obtener lote_id directamente de tmp_compras, no del request
            $lote_id = $tmp_compra->lote_id;

            $lote = Lote::findOrFail($lote_id);
            $producto = Producto::findOrFail($tmp_compra->producto_id);

            // Validación de stock máximo
          //  $stock_actual = Lote::where('producto_id', $producto->id)->sum('cantidad');
           // $nuevo_stock = $stock_actual + $lote->cantidad;

           // if ($producto->stock_maximo !== null && $producto->stock_maximo > 0 && $nuevo_stock > $producto->stock_maximo) {
            //    throw new \Exception("No se puede registrar la compra del producto '{$producto->nombre}'. 
             //       Stock máximo permitido: {$producto->stock_maximo}. 
             //       Stock actual: {$stock_actual}. 
              //      Con esta compra sería: {$nuevo_stock}.");
          //  }

            // Crear detalle de compra
            DetalleCompra::create([
                'cantidad' => $lote->cantidad,
                'compra_id' => $compra->id,
                'lote_id' => $lote_id,
                'producto_id' => $tmp_compra->producto_id
            ]);
        }

        // Eliminar temporales
        TmpCompra::where('session_id', $session_id)->delete();

        DB::commit();

        return redirect()->route('admin.compras.index')
            ->with('status', 'Compra registrada correctamente');

    } catch (\Exception $e) {
        DB::rollBack();

        return back()->withInput()
            ->with('status', 'Error al registrar la compra: ' . $e->getMessage());
    }
}
 public function agregarLote(Request $request)
{
    $validated = $request->validate([
        'numero_lote' => 'required|string|unique:lotes,numero_lote',
        'cantidad' => 'required|integer|min:1',
        'fecha_ingreso' => 'required|date',
        'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_ingreso',
        'precio_compra' => 'required|numeric|min:0',
        'precio_venta' => 'required|numeric|min:0',
        'producto_id' => 'required|exists:productos,id',
        'tmp_compra_id' => 'required|exists:tmp_compras,id'
    ]);

    // AGREGAR ESTA VALIDACIÓN DEL STOCK MÁXIMO =====
    $producto = Producto::find($validated['producto_id']);
    $stockActual = $producto->lotes()->sum('cantidad');
    $nuevaCantidad = $validated['cantidad'];
    $totalFinal = $stockActual + $nuevaCantidad;
    
    if ($totalFinal > $producto->stock_maximo) {
        return response()->json([
            'success' => false,
            'message' => "No puedes agregar {$nuevaCantidad} unidades. 
                         Stock actual: {$stockActual} unidades + {$nuevaCantidad} unidades = {$totalFinal} unidades
                         Stock máximo permitido: {$producto->stock_maximo} unidades"
        ], 422);
    }


    $precioCompraUnitario = $validated['precio_compra'] / $validated['cantidad'];

    if ($validated['precio_venta'] <= $precioCompraUnitario) {
        return response()->json([
            'success' => false,
            'message' => 'El precio de venta debe ser mayor al precio de compra unitario (Bs ' . number_format($precioCompraUnitario, 2) . ').'
        ], 422);
    }

    try {
        DB::beginTransaction();

        // Crear el nuevo lote
        $lote = Lote::create([
            'producto_id' => $validated['producto_id'],
            'numero_lote' => $validated['numero_lote'],
            'fecha_ingreso' => $validated['fecha_ingreso'],
            'fecha_vencimiento' => $validated['fecha_vencimiento'],
            'cantidad' => $validated['cantidad'],
            'cantidad_inicial' => $validated['cantidad'],
            'activo' => true,
            'precio_compra' => $validated['precio_compra'],
            'precio_venta' => $validated['precio_venta'],
            'precio_compra_unitario' => $precioCompraUnitario,
        ]);

        // ACTUALIZAR EL TMP_COMPRA CON EL LOTE CREADO
        TmpCompra::where('id', $validated['tmp_compra_id'])
                ->update(['lote_id' => $lote->id]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Lote creado correctamente',
            'lote_id' => $lote->id,
            'tmp_compra_id' => $validated['tmp_compra_id'],
            'producto_id' => $validated['producto_id'],
            'numero_lote' => $validated['numero_lote'],
            'precio_venta' => $validated['precio_venta'],
            'cantidad_lote' => $validated['cantidad'],
            'precio_compra_unitario' => $precioCompraUnitario
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error al crear lote: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error al crear el lote: ' . $e->getMessage()
        ], 500);
    }
}

    public function getStockTotalAttribute()
    {
        return Cache::remember("producto_{$this->id}_stock", now()->addHours(1), function () {
            return $this->lotes()->sum('cantidad');
        });
    }
    public function mostrarTmpCompras()
    {
        $tmpCompras = TmpCompra::with('producto')->where('user_id', auth()->id())->get();

        $lotesPorProducto = Lote::latest('id')->get()->groupBy('producto_id');


        return view('compras.create', compact('tmpCompras', 'lotesPorProducto'));

    }


    public function show($id)
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Compras', 'url' => route('admin.compras.index')],
            ['name' => 'Ver Compra', 'url' => route('admin.compras.create')],

        ];
        $compra = Compra::with('detalles', 'laboratorio')->findOrFail($id);
        return view('admin.compras.show', compact('breadcrumb', 'compra'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Ver Compra', 'url' => route('admin.compras.show', $id)],
            ['name' => 'Editar Compra', 'url' => ''],

        ];
        $compra = Compra::with('detalles', 'laboratorio')->findOrFail($id);
        $laboratorios = Laboratorio::all();
        $productos = Producto::all();
        return view('admin.compras.edit', compact('breadcrumb', 'compra', 'laboratorios', 'productos'));
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validación de los datos de entrada
        $request->validate([
            'fecha' => 'required|date',
            'comprobante' => 'required|string|max:50',
            'precio_total' => 'required',
            'laboratorio_id' => 'required|exists:laboratorios,id', // evita null
        ]);

        $compra = Compra::findOrFail($id);

        // Asegurarse de que precio_total sea decimal
        $precio_total = str_replace(',', '', $request->precio_total); // elimina comas
        $precio_total = floatval($precio_total);

        $compra->fecha = $request->fecha;
        $compra->comprobante = $request->comprobante;
        $compra->precio_total = $precio_total;
        $compra->sucursal_id = Auth::user()->sucursal_id;
        $compra->laboratorio_id = $request->laboratorio_id;

        $compra->save();

        // Opcional: limpiar temporales de sesión si aplica
        // $session_id = session()->getId();
        // TmpCompra::where('session_id', $session_id)->delete();

        return redirect()->route('admin.compras.index')
            ->with('status', 'Compra actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
public function destroy($id)
{
    DB::beginTransaction();
    try {
        // Obtener la compra con detalles
        $compra = Compra::with(['detalles'])->findOrFail($id);

        // ara cada detalle, eliminar solo el lote específico de ESTA compra
        foreach ($compra->detalles as $detalle) {
           
            if ($detalle->lote_id) {
                Lote::where('id', $detalle->lote_id)->delete();
                logger("Eliminado lote ID: {$detalle->lote_id} del producto en detalle: {$detalle->id}");
            }
        }

       
        $compra->detalles()->delete();

      
        $compra->delete();

        DB::commit();

        return redirect()->route('admin.compras.index')
            ->with('success', 'Compra eliminada correctamente. Solo se removieron los lotes específicos de esta compra.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Error eliminando compra {$id}: " . $e->getMessage());

        return redirect()->back()
            ->with('error', 'No se pudo eliminar: ' . $e->getMessage());
    }
}
    public function agregarTmp(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|numeric|min:1'
        ]);

        // Verificar si ya existe
        $tmp = TmpCompra::where('user_id', auth()->id())
            ->where('producto_id', $request->producto_id)
            ->first();

        if ($tmp) {
            $tmp->cantidad += $request->cantidad;
            $tmp->save();
        } else {
            TmpCompra::create([
                'user_id' => auth()->id(),
                'producto_id' => $request->producto_id,
                'cantidad' => $request->cantidad
            ]);
        }

        return response()->json(['success' => true]);
    }



    public function eliminarTmp(Request $request)
{
    $request->validate([
        'producto_id' => 'required|exists:productos,id'
    ]);

    // Eliminar de la tabla temporal
    TmpCompra::where('user_id', auth()->id())
        ->where('producto_id', $request->producto_id)
        ->delete();

    // Eliminar también de lotes
    Lote::where('producto_id', $request->producto_id)->delete();

    return response()->json(['success' => true]);
}


    public function actualizarTmp(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|numeric|min:1'
        ]);

        TmpCompra::where('user_id', auth()->id())
            ->where('producto_id', $request->producto_id)
            ->update(['cantidad' => $request->cantidad]);

        return response()->json(['success' => true]);
    }




public function reporteDiario(Request $request)
{
    try {
        $tipo = $request->input('tipo', 'dia'); // 'dia', 'semana', 'mes', 'anio', 'rango'
        $sucursal = Sucursal::find(Auth::user()->sucursal_id);

        $query = Compra::with(['laboratorio'])
            ->where('sucursal_id', Auth::user()->sucursal_id);

        $hoy = now()->format("Y-m-d");

        switch ($tipo) {
            case 'dia':
                $inicio = $request->input('fecha', $hoy);
                $fin = $inicio;
                $query->whereDate('fecha', $inicio);
                $titulo = "REPORTE DE COMPRAS DEL DÍA";
                break;

            case 'semana':
                $inicio = now()->startOfWeek()->format("Y-m-d");
                $fin = now()->endOfWeek()->format("Y-m-d");
                $query->whereBetween('fecha', [$inicio, $fin]);
                $titulo = "REPORTE SEMANAL DE COMPRAS";
                break;

            case 'mes':
                $inicio = now()->startOfMonth()->format("Y-m-d");
                $fin = now()->endOfMonth()->format("Y-m-d");
                $query->whereBetween('fecha', [$inicio, $fin]);
                $titulo = "REPORTE MENSUAL DE COMPRAS";
                break;

            case 'anio':
                $inicio = now()->startOfYear()->format("Y-m-d");
                $fin = now()->endOfYear()->format("Y-m-d");
                $query->whereBetween('fecha', [$inicio, $fin]);
                $titulo = "REPORTE ANUAL DE COMPRAS";
                break;

            case 'rango':
                $inicio = $request->fecha_inicio;
                $fin = $request->fecha_fin;

                if (!$inicio || !$fin) {
                    throw new \Exception("Debe seleccionar un rango de fechas válido");
                }

                $query->whereBetween('fecha', [$inicio, $fin]);
                $titulo = "REPORTE DE COMPRAS POR RANGO";
                break;

            default:
                throw new \Exception("Tipo de reporte inválido");
        }

        $compras = $query->orderBy('fecha', 'desc')->get();

        if ($compras->count() === 0) {
            throw new \Exception("No se encontraron compras en este periodo");
        }

        $totalCompras = $compras->count();
        $totalEgresos = $compras->sum('precio_total');

        $data = [
            'compras' => $compras,
            'sucursal' => $sucursal,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'inicio' => $inicio,
            'fin' => $fin,
            'fecha' => $inicio, // para compatibilidad con la vista
            'fecha_generacion' => now()->format('d/m/Y H:i:s'),
            'totalCompras' => $totalCompras,
            'totalEgresos' => $totalEgresos,
        ];

        $pdf = PDF::loadView('admin.compras.reporte_diario_pdf', $data)
                 ->setPaper('a4', 'portrait');

        return $pdf->download("reporte_compras_{$tipo}.pdf");

    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
        ], 500);
    }
}


public function reporteDiaario(Request $request)
{
    try {
        // DEBUG: mostrar información básica
        logger('=== INICIANDO REPORTE COMPRAS DÍA ===');
        logger('Fecha: ' . $request->input('fecha', now()->format('Y-m-d')));
        logger('Sucursal ID: ' . Auth::user()->sucursal_id);
        logger('Usuario: ' . Auth::user()->name);

        // Obtener fecha de hoy
        $fecha = $request->input('fecha', now()->format('Y-m-d'));
        
        // DEBUG: Verificar consulta de COMPRAS
        $comprasQuery = Compra::with(['detallesCompra.producto', 'detallesCompra.lote', 'proveedor'])
            ->where('sucursal_id', Auth::user()->sucursal_id)
            ->whereDate('fecha', $fecha)
            ->orderBy('fecha', 'desc');

        logger('SQL: ' . $comprasQuery->toSql());
        logger('Bindings: ' . json_encode($comprasQuery->getBindings()));
        
        $compras = $comprasQuery->get();
        
        logger('Compras encontradas: ' . $compras->count());

        // DEBUG: Verificar si hay compras
        if ($compras->count() === 0) {
            logger('NO HAY COMPRAS para esta fecha: ' . $fecha);
            // Forzar error para ver qué pasa
            throw new \Exception("No hay compras registradas para la fecha: " . $fecha);
        }

        // Calcular estadísticas
        $totalCompras = $compras->count();
        $totalEgresos = $compras->sum('precio_total');
        $totalProductos = $compras->sum(function($compra) {
            return $compra->detallesCompra->sum('cantidad');
        });

        logger('Estadísticas - Compras: ' . $totalCompras . ', Egresos: ' . $totalEgresos . ', Productos: ' . $totalProductos);

        // Obtener sucursal
        $sucursal = Sucursal::find(Auth::user()->sucursal_id);
        
        if (!$sucursal) {
            throw new \Exception("No se encontró la sucursal con ID: " . Auth::user()->sucursal_id);
        }

        logger('Sucursal: ' . $sucursal->nombre);

        $data = [
            'compras' => $compras,
            'fecha' => $fecha,
            'sucursal' => $sucursal,
            'totalCompras' => $totalCompras,
            'totalEgresos' => $totalEgresos,
            'totalProductos' => $totalProductos,
            'fecha_generacion' => now()->format('d/m/Y H:i:s')
        ];

        // DEBUG: Verificar si la vista existe
        if (!view()->exists('admin.compras.reporte_diario_pdf')) {
            throw new \Exception("La vista no existe: admin.compras.reporte_diario_pdf");
        }
        
        logger('Vista encontrada: admin.compras.reporte_diario_pdf');

        // DEBUG: Antes de generar PDF
        logger('Generando PDF...');

        // Generar PDF
        $pdf = PDF::loadView('admin.compras.reporte_diario_pdf', $data)
                 ->setPaper('a4', 'portrait');

        logger('PDF generado exitosamente');

        // Cambiar nombre del archivo descargado
        return $pdf->download('reporte_diario_pdf_' . $fecha . '.pdf');

    } catch (\Exception $e) {
        // DEBUG: Mostrar error COMPLETO
        logger('=== ERROR EN REPORTE ===');
        logger('Mensaje: ' . $e->getMessage());
        logger('Archivo: ' . $e->getFile());
        logger('Línea: ' . $e->getLine());
        logger('Trace: ' . $e->getTraceAsString());

        // Retornar error en pantalla para verlo directamente
        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
}



    public function reporte($tipo, Request $request)
    {
        // Validar el tipo de reporte
        if (!in_array($tipo, ['pdf', 'excel', 'csv'])) {
            abort(400, 'Tipo de reporte no válido');
        }

        
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');
        $laboratorio_id = $request->input('laboratorio_id');

        // Consulta base
        $query = Compra::with(['detalles', 'laboratorio'])
            ->where('sucursal_id', Auth::user()->sucursal_id);

       
        if ($fecha_inicio && $fecha_fin) {
            $query->whereBetween('fecha', [$fecha_inicio, $fecha_fin]);
        }

        if ($laboratorio_id) {
            $query->where('laboratorio_id', $laboratorio_id);
        }

        $compras = $query->get();

      
        if ($compras->isEmpty()) {
            return back()->with('error', 'No hay compras con los filtros seleccionados');
        }

        switch ($tipo) {
            case 'pdf':
                return $this->generarPDF($compras);
            case 'excel':
                return $this->generarExcel($compras);
            case 'csv':
                return $this->generarCSV($compras);
        }
    }


    private function generarPDF($compras)
    {
        $pdf = PDF::loadView('admin.compras.reporte', [
            'compras' => $compras,
            'fecha_generacion' => now()->format('d/m/Y H:i:s')
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reporte_compras_' . now()->format('YmdHis') . '.pdf');
    }

    private function generarExcel($compras)
    {
        $data = $compras->map(function ($compra) {
            return [
                'Fecha' => $compra->fecha,
                'Comprobante' => $compra->comprobante,
                'Laboratorio' => $compra->laboratorio->nombre ?? 'N/A',
                'Total' => number_format($compra->precio_total, 2),
                'Productos' => $compra->detalles->count(),
                'Cantidad Total' => $compra->detalles->sum('cantidad')
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
                    'Comprobante',
                    'Laboratorio',
                    'Total (Bs)',
                    'N° Productos',
                    'Cantidad Total'
                ];
            }

            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
            {
                return [
                    
                    1 => [
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF'],
                            'size' => 12
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '3498DB'] 
                        ],
                        'alignment' => [
                            'horizontal' => 'center',
                            'vertical' => 'center'
                        ]
                    ],
                    
                    'A2:F' . $sheet->getHighestRow() => [
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
                        'alignment' => [
                            'horizontal' => 'left'
                        ]
                    ],
                    
                    'D2:D' . $sheet->getHighestRow() => [
                        'numberFormat' => [
                            'formatCode' => '#,##0.00'
                        ]
                    ]
                ];
            }

            public function columnWidths(): array
            {
                return [
                    'A' => 15,  // Fecha
                    'B' => 20,  // Comprobante
                    'C' => 25,  // Laboratorio
                    'D' => 15,  // Total
                    'E' => 12,  // Productos
                    'F' => 15   // Cantidad
                ];
            }
            },
            'reporte_compras_' . now()->format('YmdHis') . '.xlsx'
        );
    }

   

  public function pdf($id)
    {
        try {
            // 1. Obtener datos básicos
            $id_sucursal = Auth::user()->sucursal_id;
            $sucursal = Sucursal::findOrFail($id_sucursal);

            // 2. Obtener la compra con relaciones 
            $compra = Compra::with([
                'detalles.producto.lotes' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'laboratorio'
            ])
                ->where('sucursal_id', $id_sucursal)
                ->findOrFail($id);

            // 3. Calcular subtotal basado en lotes
            $subtotal_calculado = 0;
            foreach ($compra->detalles as $detalle) {
                $lote = $detalle->producto->lotes->first();
                $detalle->precio_compra_calculado = $lote ? $lote->precio_compra : 0;
                $detalle->subtotal_calculado = $detalle->cantidad * $detalle->precio_compra_calculado;
                $subtotal_calculado += $detalle->subtotal_calculado;
            }

          
            $literal = $this->numerosALetrasConDecimales($compra->precio_total);

            
            $pdf = PDF::loadView('admin.compras.pdf', [
                'sucursal' => $sucursal,
                'compra' => $compra,
                'literal' => $literal,
                'subtotal_calculado' => $subtotal_calculado,
                'fecha_generacion' => now()->format('d/m/Y H:i')
            ])->setPaper([0, 0, 250.77, 600], 'portrait');

            return $pdf->stream("compra-{$compra->comprobante}.pdf");

        } catch (\Exception $e) {
            Log::error("Error al generar PDF de compra: " . $e->getMessage());
            return redirect()->route('admin.compras.index')
                ->with('error', 'No se pudo generar el reporte: ' . $e->getMessage());
        }
    }  

  

private function numerosALetrasConDecimales($numero)
{
    $unidades = [
        '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve', 'diez',
        'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve', 'veinte'
    ];
    $decenas = ['', '', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'];
    $centenas = ['', 'cien', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'];

    
    $partes = explode('.', number_format(abs($numero), 2, '.', ''));
    $entero = intval($partes[0]);
    $decimal = intval($partes[1]);

   
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
            return $centenas[$c] . ($r ? ' ' . $convertir($r) : '');
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

    return ($numero < 0 ? 'Menos ' : '') . ucfirst("$textoEntero con $textoDecimal");
}


}










