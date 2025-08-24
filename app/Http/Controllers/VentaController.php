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
use Illuminate\Support\Facades\DB; // ¡Este es el import que faltaba!
use Illuminate\Support\Facades\Log;


use Illuminate\Support\Facades\Storage;


use Maatwebsite\Excel\Facades\Excel;

use Carbon\Carbon;

use DatePeriod; // Importación añadida
use DateInterval; // Importación añadida
use DateTime; // Importación añadida
// Asegúrate de tener este modelo para acceder a los datos de ingresos

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
            ->orderBy('fecha', 'desc')  // Ordenar por fecha descendente
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
                // Crear detalle venta (manteniendo tu lógica actual)
                $detalle_venta = new DetalleVenta();
                $detalle_venta->cantidad = $tmp_venta->cantidad;
                $detalle_venta->venta_id = $ventas->id;
                $detalle_venta->producto_id = $tmp_venta->producto_id;
                $detalle_venta->save();

                // PEPS para descontar de múltiples lotes
                $cantidad_restante = $tmp_venta->cantidad;

                // Obtener lotes ordenados por fecha de ingreso (más antiguos primero)
                $lotes = Lote::where('producto_id', $tmp_venta->producto_id)
                    ->where('cantidad', '>', 0)
                    ->orderBy('fecha_ingreso', 'asc')
                    ->get();

                foreach ($lotes as $lote) {
                    if ($cantidad_restante <= 0)
                        break;

                    $cantidad_a_descontar = min($lote->cantidad, $cantidad_restante);

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

            return redirect()->route('admin.ventas.index')
                ->with('status', 'Se registró la venta correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('status', 'Error al registrar la venta: ' . $e->getMessage());
        }
    }





    public function pdf($id)
    {
        $id_sucursal = Auth::user()->sucursal_id;
        $sucursal = Sucursal::where('id', $id_sucursal)->first();

        $venta = Venta::with(['detallesVenta.producto', 'cliente'])->findOrFail($id);

        // Calcular el total sumando los subtotales
        $total = $venta->detallesVenta->sum(function ($detalle) {
            $lote = $detalle->producto->lotes()->latest()->first();
            $precio = $lote ? $lote->precio_venta : $detalle->producto->precio_venta;
            return $detalle->cantidad * $precio;
        });

        // Convertir a letras (variable ya contiene el string)
        $literal = $this->numerosALetrasConDecimales($total);

        return PDF::loadView('admin.ventas.pdf', [
            'sucursal' => $sucursal,
            'venta' => $venta,
            'literal' => $literal, // String con el monto en letras
            'total' => $total
        ])->setPaper([0, 0, 250.77, 600], 'portrait')->stream();
    }

    private function numerosALetrasConDecimales($numero)
    {
        $formatter = new NumberFormatter("es", NumberFormatter::SPELLOUT);
        $partes = explode('.', number_format(abs($numero), 2, '.', ''));

        $entero = $partes[0] == 1 ? 'un boliviano' : $formatter->format($partes[0]) . ' bolivianos';
        $decimal = $partes[1] == '00' ? 'exactos' : $formatter->format($partes[1]) . ' centavos';

        return ($numero < 0 ? 'Menos ' : '') . ucfirst("$entero con $decimal");
    }







    public function show($id)
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Ventas', 'url' => route('admin.ventas.index')],
            ['name' => 'Ver Venta', 'url' => route('admin.ventas.index')],

        ];
        $venta = Venta::with('detallesVenta', 'cliente')->findOrfail($id);
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

            // 1. Restaurar stock en lotes
            foreach ($venta->detallesVenta as $detalle) {
                $lote = $detalle->producto->lotes->first();
                if ($lote) {
                    $lote->increment('cantidad', $detalle->cantidad);
                }
            }

            // 2. Eliminar en cascada
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

        // Consulta base
        $query = Venta::with(['detallesVenta', 'cliente'])
            ->where('sucursal_id', Auth::user()->sucursal_id);

        // Aplicar filtros
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
                    // Alineación izquierda para cliente
                    'C2:C' . $sheet->getHighestRow() => [
                        'alignment' => ['horizontal' => 'left']
                    ],
                    // Formato numérico para total
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

    private function generarCSV($ventas): BinaryFileResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reporte_ventas_' . now()->format('YmdHis') . '.csv"',
        ];

        $callback = function () use ($ventas) {
            $file = fopen('php://output', 'w');

            // Encabezados
            fputcsv($file, ['Fecha', 'Cliente', 'Total', 'Productos', 'Cantidad Total']);

            // Datos
            foreach ($ventas as $venta) {
                fputcsv($file, [
                    $venta->fecha,
                    $venta->cliente->nombre_cliente ?? 'Sin cliente',
                    $venta->precio_total,
                    $venta->detallesVenta->count(),
                    $venta->detallesVenta->sum('cantidad')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }





}
