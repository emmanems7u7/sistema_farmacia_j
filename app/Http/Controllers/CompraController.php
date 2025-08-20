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
use Illuminate\Support\Facades\Auth; // Importar Auth
use Illuminate\Support\Facades\DB; // ¡Este es el import que faltaba!
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Storage;

use Maatwebsite\Excel\Facades\Excel;

use Carbon\Carbon;

use DatePeriod; // Importación añadida
use DateInterval; // Importación añadida
use DateTime; // Importación añadida
// Asegúrate de tener este modelo para acceder a los datos de ingresos
use PDF; 
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cajaAbierto = Caja::whereNull('fecha_cierre')->first();
        $compras = Compra::with(['detalles','laboratorio'])->get();
        $sucursals = Sucursal::all();
        
        return view('admin.compras.index', compact('compras','cajaAbierto','sucursals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productos = Producto::where('sucursal_id', Auth::user()->sucursal_id)->get();
     //   $proveedores = Proveedor::where('sucursal_id', Auth::user()->sucursal_id)->get();
        $laboratorios = Laboratorio::all();
    $session_id = session()->getId();
    $tmp_compras = TmpCompra::where('session_id',$session_id)->get();
$lotesPorProducto = \App\Models\Lote::latest('id')->get()->groupBy('producto_id');
 $productosConLotes = Producto::with('lotes')->get();

        return view('admin.compras.create', compact('productos', 'laboratorios', 'tmp_compras', 'lotesPorProducto','productosConLotes'));

    }
    
    
public function store(Request $request)
{
    // Validación (sin cambios)
    $request->validate([
        'fecha' => 'required|date',
        'comprobante' => 'required|string|max:50',
        'precio_total' => 'required|numeric|min:0',
        'lotes' => 'required|array',
        'lotes.*' => 'required|exists:lotes,id'
    ]);

    $caja = Caja::whereNull('fecha_cierre')->firstOrFail();
    
    DB::beginTransaction();

    try {
        // Crear la compra (sin cambios)
        $compra = Compra::create([
            'fecha' => $request->fecha,
            'comprobante' => $request->comprobante,
            'precio_total' => $request->precio_total,
            'sucursal_id' => Auth::user()->sucursal_id,
            'laboratorio_id' => $request->laboratorio_id
        ]);

        // Movimiento de caja (sin cambios)
        MovimientoCaja::create([
            'tipo' => "EGRESO",
            'monto' => $request->precio_total,
            'descripcion' => "Compra de productos",
            'fecha_movimiento' => $request->fecha_movimiento ?? now(),
            'caja_id' => $caja->id
        ]);

        // Procesar productos temporales
        $session_id = session()->getId();
        $tmp_compras = TmpCompra::where('session_id', $session_id)->get();

        foreach($tmp_compras as $tmp_compra) {
            $lote_id = $request->lotes[$tmp_compra->producto_id] ?? null;
            
            if (!$lote_id) {
                throw new \Exception("No se seleccionó lote para el producto ID: {$tmp_compra->producto_id}");
            }

            // Crear detalle de compra
            DetalleCompra::create([
                'cantidad' => $tmp_compra->cantidad,
                'compra_id' => $compra->id,
                'producto_id' => $tmp_compra->producto_id
            ]);

            // SOLO actualizar stock en el lote (eliminada la línea de producto)
            Lote::where('id', $lote_id)->increment('cantidad', $tmp_compra->cantidad);
        }

        // Eliminar temporales
        TmpCompra::where('session_id', $session_id)->delete();

        DB::commit();

        return redirect()->route('admin.compras.index')
            ->with('mensaje', 'Compra registrada correctamente')
            ->with('icono', 'success');

    } catch (\Exception $e) {
        DB::rollBack();
        
        return back()->withInput()
            ->with('mensaje', 'Error al registrar la compra: ' . $e->getMessage())
            ->with('icono', 'error');
    }
}
public function agregarLote(Request $request)
{
    $validated = $request->validate([
        'numero_lote' => 'required|string|unique:lotes,numero_lote',
        'cantidad' => 'required|integer|min:1',
        'fecha_ingreso' => 'required|date',
        'fecha_vencimiento' => 'required|date|after_or_equal:fecha_ingreso',
        'precio_compra' => 'required|numeric|min:0',
        'precio_venta' => 'required|numeric|min:0|gte:precio_compra',
        'producto_id' => 'required|exists:productos,id',
    ]);

    try {
        DB::beginTransaction();

        // Obtener la sucursal del usuario autenticado
        $sucursal_id = auth()->user()->sucursal_id;

        // Crear el nuevo lote con sucursal
        Lote::create([
            'numero_lote' => $validated['numero_lote'],
            'fecha_ingreso' => $validated['fecha_ingreso'],
            'fecha_vencimiento' => $validated['fecha_vencimiento'],
            'cantidad' => $validated['cantidad'],
            'cantidad_inicial' => $validated['cantidad'],
            'precio_compra' => $validated['precio_compra'],
            'precio_venta' => $validated['precio_venta'],
            'producto_id' => $validated['producto_id'],
            'sucursal_id' => $sucursal_id, // Asignar sucursal automáticamente
            'activo' => true,
            'session_id' => session()->getId(),
        ]);

        DB::commit();

        return back()->with('alert', [
            'type' => 'success',
            'message' => 'Lote creado correctamente'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error al crear lote: '.$e->getMessage());
        
        return back()->with('alert', [
            'type' => 'error',
            'message' => 'Error al crear el lote: '.$e->getMessage()
        ]);
    }
}

public function getStockTotalAttribute()
{
    return Cache::remember("producto_{$this->id}_stock", now()->addHours(1), function() {
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
        //
        $compra = Compra::with('detalles','laboratorio')->findOrFail($id);
        return view('admin.compras.show',compact('compra'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    
    
    {
    
        //
        $compra = Compra::with('detalles','laboratorio')->findOrFail($id);
        $laboratorios = Laboratorio::all();
        $productos = Producto::all();
        return view('admin.compras.edit',compact('compra','laboratorios','productos'));
    }
    


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)

    {
         // Validación de los datos de entrada
         $request->validate([
        'fecha' => 'required',
        'comprobante' => 'required',
        'precio_total' => 'required', //
    ]);

    // Crear un nuevo laboratorio
    $compra = Compra::find($id);
    $compra->fecha = $request->fecha;
    $compra->comprobante = $request->comprobante;
    $compra->precio_total = $request->precio_total;

    $compra->sucursal_id = Auth::user()->sucursal_id;
    $compra->laboratorio_id = $request->laboratorio_id;
    $compra->save();

    $session_id = session()->getId();


      
    
        return redirect()->route('admin.compras.index')
        ->with('mensaje', 'Compra actualizada correctamente')
        ->with('icono','success');

    }
    /**
     * Remove the specified resource from storage.
     */
public function destroy($id) {
    DB::beginTransaction();
    try {
        // 1. Obtener la compra con sus detalles
        $compra = Compra::with(['detalles'])->findOrFail($id);
        
        // 2. Obtener los IDs de productos involucrados
        $productosIds = $compra->detalles->pluck('producto_id')->unique();
        
        // 3. Eliminar lotes asociados a esos productos
        Lote::whereIn('producto_id', $productosIds)->delete();
        
        // 4. Eliminar detalles de la compra
        $compra->detalles()->delete();
        
        // 5. Eliminar la compra
        $compra->delete();
        
        DB::commit();
        
        return redirect()->route('admin.compras.index')
               ->with('success', 'Compra eliminada con sus lotes asociados');
               
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

    TmpCompra::where('user_id', auth()->id())
             ->where('producto_id', $request->producto_id)
             ->delete();

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


public function reporte($tipo, Request $request)
{
    // Validar el tipo de reporte
    if (!in_array($tipo, ['pdf', 'excel', 'csv'])) {
        abort(400, 'Tipo de reporte no válido');
    }

    // Obtener filtros
    $fecha_inicio = $request->input('fecha_inicio');
    $fecha_fin = $request->input('fecha_fin');
    $laboratorio_id = $request->input('laboratorio_id');

    // Consulta base
    $query = Compra::with(['detalles', 'laboratorio'])
                  ->where('sucursal_id', Auth::user()->sucursal_id);

    // Aplicar filtros
    if ($fecha_inicio && $fecha_fin) {
        $query->whereBetween('fecha', [$fecha_inicio, $fecha_fin]);
    }

    if ($laboratorio_id) {
        $query->where('laboratorio_id', $laboratorio_id);
    }

    $compras = $query->get();

    // Verificar si hay datos
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
    
    return $pdf->download('reporte_compras_'.now()->format('YmdHis').'.pdf');
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
        new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection,
                               \Maatwebsite\Excel\Concerns\WithHeadings,
                               \Maatwebsite\Excel\Concerns\WithStyles,
                               \Maatwebsite\Excel\Concerns\ShouldAutoSize,
                               \Maatwebsite\Excel\Concerns\WithColumnWidths {
            
            private $data;
            
            public function __construct($data) {
                $this->data = collect($data);
            }
            
            public function collection() {
                return $this->data;
            }
            
            public function headings(): array {
                return [
                    'Fecha',
                    'Comprobante',
                    'Laboratorio',
                    'Total (Bs)',
                    'N° Productos',
                    'Cantidad Total'
                ];
            }
            
            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet) {
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
                    // Alineación izquierda para laboratorio
                    'C2:C' . $sheet->getHighestRow() => [
                        'alignment' => [
                            'horizontal' => 'left'
                        ]
                    ],
                    // Formato numérico para total
                    'D2:D' . $sheet->getHighestRow() => [
                        'numberFormat' => [
                            'formatCode' => '#,##0.00'
                        ]
                    ]
                ];
            }
            
            public function columnWidths(): array {
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

private function generarCSV($compras): BinaryFileResponse
{
    return $this->generarExcel($compras)
        ->setContentDisposition('attachment', 'reporte_compras_'.now()->format('YmdHis').'.csv');
}



public function pdf($id) 
{
    try {
        // 1. Obtener datos básicos
        $id_sucursal = Auth::user()->sucursal_id;
        $sucursal = Sucursal::findOrFail($id_sucursal);
        
        // 2. Obtener la compra con relaciones (incluyendo lotes)
        $compra = Compra::with([
                'detalles.producto.lotes' => function($query) {
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

        // 4. Convertir total a letras
        $literal = $this->numerosALetrasConDecimales($compra->precio_total);

        // 5. Generar PDF con datos
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

private function numerosALetrasConDecimales($numero) {
    $formatter = new NumberFormatter("es", NumberFormatter::SPELLOUT);
    $partes = explode('.', number_format($numero, 2, '.', ''));
    $entero = $formatter->format($partes[0]);
    $decimal = $formatter->format($partes[1]);
    return ucfirst("$entero con $decimal/100");
}
}










