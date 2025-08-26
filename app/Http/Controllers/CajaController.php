<?php

namespace App\Http\Controllers;


use App\Models\Caja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PDF;
use App\Models\Sucursal;
use App\Models\MovimientoCaja;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Maatwebsite\Excel\Facades\Excel;
use NumberToWords\NumberToWords;

use NumberFormatter;

use Carbon\Carbon;

use DatePeriod;
use DateInterval;
use DateTime;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
class CajaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Caja', 'url' => route('admin.compras.index')],


        ];
        $cajaAbierto = Caja::whereNull('fecha_cierre')->first();
        $cajas = Caja::with('movimientos')->get();

        foreach ($cajas as $caja) {
            $caja->total_ingresos = $caja->movimientos->where('tipo', 'INGRESO')->sum('monto');
            $caja->total_egresos = $caja->movimientos->where('tipo', 'EGRESO')->sum('monto');
        }
        return view('admin.cajas.index', compact('breadcrumb', 'cajas', 'cajaAbierto'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Compras', 'url' => route('admin.compras.index')],
            ['name' => 'Apertura de Caja', 'url' => route('admin.compras.create')],

        ];
        return view('admin.cajas.create', compact('breadcrumb'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        //$datos = request()->all();
        //return response()->json($datos);

        $request->validate([
            'fecha_apertura' => 'required|date',
        ]);

        $caja = new Caja();
        $caja->fecha_apertura = $request->fecha_apertura;
        $caja->monto_inicial = $request->monto_inicial;
        $caja->descripcion = $request->descripcion;
        $caja->sucursal_id = Auth::user()->sucursal_id; // asignar sucursal_id

        $caja->save(); // guardar la nueva caja en la base de datos

        return redirect()->route('admin.cajas.index')->with('success', 'Caja registrada exitosamente.');

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $caja = Caja::with([
            'movimientos.venta.detalles.producto', // Cambié detalleventa a detalles
            'movimientos.venta.cliente',
            'movimientos.compra.detalles.producto', // Cambié detallecompra a detalles
            'movimientos.compra.proveedor',
            'sucursal'
        ])->findOrFail($id);

        // Debug adicional para verificar datos
        logger('Detalles de ventas cargados:', [
            'ventas' => $caja->movimientos->where('tipo', 'INGRESO')
                ->pluck('venta')->filter()->pluck('detalles')
        ]);

        return view('admin.cajas.show', [
            'caja' => $caja,
            'totalIngresos' => $caja->movimientos->where('tipo', 'INGRESO')->sum('monto'),
            'totalEgresos' => $caja->movimientos->where('tipo', 'EGRESO')->sum('monto')
        ]);
    }

    public function edit(Caja $caja, $id)
    {
        //
        $caja = Caja::finf($id)->first();
        return view('admin.cajas.edit', compact('caja'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'fecha_apertura' => 'required|date',
        ]);

        $caja = Caja::find($id);
        $caja->fecha_apertura = $request->fecha_apertura;
        $caja->monto_inicial = $request->monto_inicial;
        $caja->descripcion = $request->descripcion;
        $caja->sucursal_id = 1; // asignar sucursal_id

        $caja->save(); // guardar la nueva caja en la base de datos

        return redirect()->route('admin.cajas.index')->with('success', 'Caja actualizada exitosamente.');

    }

    /**
     * Remove the specified resource from storage.
     */

    public function ingresoegreso($id)
    {
        //
        $caja = Caja::find($id);
        return view('admin.cajas.ingresos_egreso', compact('caja'));
    }

    public function store_ingresos_egresos(Request $request)
    {
        // Validación adicional para asegurarse de que el ID no sea nulo y exista
        $request->validate([
            'monto' => 'required',
            'id' => 'required|exists:cajas,id', // Validamos que el id exista en la tabla `cajas`
        ]);

        $movimiento = new MovimientoCaja();

        $movimiento->tipo = $request->tipo;
        $movimiento->monto = $request->monto;
        $movimiento->descripcion = $request->descripcion;
        $movimiento->caja_id = $request->id; // Ahora podemos estar más seguros de que no será null

        $movimiento->save(); // Guardamos el movimiento en la base de datos
        return redirect()->route('admin.cajas.index')
            ->with('mensaje', 'Se elimino la compra correctamente')
            ->with('icono', 'success');
    }

    public function cierre($id)
    {
        //
        $caja = Caja::find($id);
        return view('admin.cajas.cierre', compact('caja'));
    }

    public function store_cierre(Request $request)
    {
        //
        $caja = Caja::find($request->id);
        $caja->fecha_cierre = $request->fecha_cierre;
        $caja->monto_final = $request->monto_final;
        $caja->save();

        return redirect()->route('admin.cajas.index')
            ->with('mensaje', 'Se registro el cierre correctamente')
            ->with('icono', 'success');

    }


    public function destroy($id)
    {
        //
        Caja::destroy($id); // Buscar el usuario por ID


        // Redirigir al índice con un mensaje de éxito
        return redirect()->route('admin.cajas.index')
            ->with('mensaje', 'Caja eliminado con éxito.')
            ->with('icono', 'success');

    }


    // Nombres constantes para archivos
    const NOMBRE_ARCHIVO_PDF = 'reporte-cajas.pdf';
    const NOMBRE_ARCHIVO_EXCEL = 'reporte-cajas.xlsx';
    const NOMBRE_ARCHIVO_CSV = 'reporte-cajas.csv';

    // Tipos de reporte permitidos
    const TIPOS_REPORTE = ['pdf', 'excel', 'csv'];

    /**
     * Genera reportes de caja en diferentes formatos
     */
    public function reportecaja($tipo, Request $request)
    {
        // Validar tipo de reporte
        if (!in_array($tipo, self::TIPOS_REPORTE)) {
            abort(400, 'Tipo de reporte no válido. Los tipos permitidos son: ' . implode(', ', self::TIPOS_REPORTE));
        }

        // Obtener parámetros de filtrado
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $clienteId = $request->input('cliente_id');

        // Consulta base con eager loading
        $query = Caja::with(['movimientos', 'sucursal'])
            ->orderBy('fecha_apertura', 'desc');

        // Aplicar filtros
        if ($fechaInicio) {
            $query->where('fecha_apertura', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->where('fecha_apertura', '<=', $fechaFin);
        }

        if ($clienteId) {
            $query->whereHas('movimientos', function ($q) use ($clienteId) {
                $q->where('cliente_id', $clienteId);
            });
        }

        $cajas = $query->get();

        // Preparar datos para el reporte
        $data = $this->prepareReportData($cajas, $fechaInicio, $fechaFin);

        // Generar el reporte según el tipo solicitado
        switch ($tipo) {
            case 'pdf':
                return $this->generatePdf($data);

            case 'excel':
                return $this->generateExcel($data);

            case 'csv':
                return $this->generateCsv($data);
        }
    }

    /**
     * Prepara los datos para los reportes
     */
    protected function prepareReportData($cajas, $fechaInicio, $fechaFin): array
    {
        $totalGeneralIngresos = 0;
        $totalGeneralEgresos = 0;

        foreach ($cajas as $caja) {
            $caja->total_ingresos = $caja->movimientos->where('tipo', 'INGRESO')->sum('monto');
            $caja->total_egresos = $caja->movimientos->where('tipo', 'EGRESO')->sum('monto');
            $caja->saldo = ($caja->monto_inicial + $caja->total_ingresos) - $caja->total_egresos;
            $caja->nombre_sucursal = optional($caja->sucursal)->nombre ?? 'N/A';

            $totalGeneralIngresos += $caja->total_ingresos;
            $totalGeneralEgresos += $caja->total_egresos;
        }

        return [
            'cajas' => $cajas,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'totalGeneralIngresos' => $totalGeneralIngresos,
            'totalGeneralEgresos' => $totalGeneralEgresos,
            'saldoGeneral' => ($totalGeneralIngresos - $totalGeneralEgresos)
        ];
    }

    /**
     * Genera el reporte en PDF
     */
    protected function generatePdf(array $data): BinaryFileResponse
    {
        $pdf = Pdf::loadView('admin.cajas.reporte', $data);
        $output = $pdf->output();

        $tempPath = tempnam(sys_get_temp_dir(), 'pdf_');
        file_put_contents($tempPath, $output);

        return new BinaryFileResponse($tempPath, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . self::NOMBRE_ARCHIVO_PDF . '"'
        ]);
    }


    /**
     * Genera el reporte en Excel
     */
    protected function generateExcel(array $data): BinaryFileResponse
    {
        $exportData = $this->prepareExportData($data);

        return Excel::download(
            new class ($exportData, $data) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle, WithEvents {

            public function __construct(
                private array $exportData,
                private array $reportData
            ) {
            }

            public function collection()
            {
                return collect($this->exportData['rows']);
            }

            public function headings(): array
            {
                return [
                    ['REPORTE DE MOVIMIENTOS DE CAJA'],
                    ['Farmacia Mariel'],
                    [
                        'Periodo: ' . $this->reportData['fechaInicio'] . ' al ' . $this->reportData['fechaFin'],
                        '',
                        '',
                        'Total Ingresos: ' . number_format($this->reportData['totalGeneralIngresos'], 2),
                        'Total Egresos: ' . number_format($this->reportData['totalGeneralEgresos'], 2),
                        'Saldo Final: ' . number_format($this->reportData['saldoGeneral'], 2)
                    ],
                    $this->exportData['headers']
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    // Estilo título principal NO OLVIDA
                    1 => [
                        'font' => [
                            'bold' => true,
                            'size' => 16,
                            'color' => ['rgb' => 'FFFFFF']
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '2C3E50']
                        ],
                        'alignment' => ['horizontal' => 'center']
                    ],
                    // Estilo subtítulo
                    2 => [
                        'font' => ['italic' => true],
                        'alignment' => ['horizontal' => 'center']
                    ],
                    // Estilo resumen financiero
                    3 => [
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F2F2F2']
                        ]
                    ],
                    // Estilo encabezados de tabla
                    4 => [
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF']
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '3498DB']
                        ]
                    ],
                    // Formato para montos
                    'D:E' => [
                        'numberFormat' => [
                            'formatCode' => '#,##0.00'
                        ]
                    ]
                ];
            }

            public function title(): string
            {
                return 'Movimientos de Caja';
            }

            public function registerEvents(): array
            {
                return [
                    AfterSheet::class => function (AfterSheet $event) {
                        // Combinar celdas para el título
                        $event->sheet->mergeCells('A1:F1');
                        $event->sheet->mergeCells('A2:F2');

                        // Autoajustar columnas
                        $columns = ['A', 'B', 'C', 'D', 'E', 'F'];
                        foreach ($columns as $column) {
                            $event->sheet->getColumnDimension($column)
                                ->setAutoSize(true);
                        }

                        // Congelar encabezados
                        $event->sheet->freezePane('A5');
                    }
                ];
            }
            },
            'reporte_caja_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Genera el reporte en CSV
     */
    protected function generateCsv(array $data): BinaryFileResponse
    {
        $exportData = $this->prepareExportData($data);

        return Excel::download(
            new class ($exportData) implements FromArray, WithHeadings {
            public function __construct(private array $data)
            {
            }

            public function array(): array
            {
                return $this->data['rows'];
            }

            public function headings(): array
            {
                return $this->data['headers'];
            }
            },
            self::NOMBRE_ARCHIVO_CSV,
            \Maatwebsite\Excel\Excel::CSV,
            ['Content-Type' => 'text/csv']
        );
    }

    /**
     * Prepara los datos para exportación (Excel/CSV)
     */
    protected function prepareExportData(array $data): array
    {
        $headers = [
            'ID',
            'Fecha Apertura',
            'Fecha Cierre',
            'Monto Inicial',
            'Total Ingresos',
            'Total Egresos',
            'Saldo',
            'Sucursal',
            'Descripción'
        ];

        $rows = [];

        foreach ($data['cajas'] as $caja) {
            $rows[] = [
                $caja->id,
                $caja->fecha_apertura->format('Y-m-d H:i:s'),
                $caja->fecha_cierre ? $caja->fecha_cierre->format('Y-m-d H:i:s') : 'Abierta',
                number_format($caja->monto_inicial, 2),
                number_format($caja->total_ingresos, 2),
                number_format($caja->total_egresos, 2),
                number_format($caja->saldo, 2),
                $caja->nombre_sucursal,
                $caja->descripcion
            ];
        }

        // Agregar totales
        $rows[] = [];
        $rows[] = [
            '',
            '',
            '',
            '',
            number_format($data['totalGeneralIngresos'], 2),
            number_format($data['totalGeneralEgresos'], 2),
            number_format($data['saldoGeneral'], 2),
            'TOTALES',
            ''
        ];

        return [
            'headers' => $headers,
            'rows' => $rows
        ];
    }


    public function pdf($id)
    {
        try {
            // 1. Obtener la caja con sus relaciones
            $caja = Caja::with(['movimientos', 'sucursal'])->findOrFail($id);



            // Convertir fechas a Carbon
            $caja->fecha_apertura = \Carbon\Carbon::parse($caja->fecha_apertura);
            if ($caja->fecha_cierre) {
                $caja->fecha_cierre = \Carbon\Carbon::parse($caja->fecha_cierre);
            }

            // 2. Obtener la sucursal desde la relación de la caja
            $sucursal = $caja->sucursal;

            // Verificar si existe la sucursal
            if (!$sucursal) {
                throw new \Exception("No se encontró la sucursal asociada a esta caja");
            }

            // 3. Calcular totales
            $totalIngresos = $caja->movimientos->where('tipo', 'INGRESO')->sum('monto');
            $totalEgresos = $caja->movimientos->where('tipo', 'EGRESO')->sum('monto');
            $saldoFinal = ($caja->monto_inicial + $totalIngresos) - $totalEgresos;

            // 4. Generar PDF
            $pdf = PDF::loadView('admin.cajas.pdf', [
                'sucursal' => $sucursal, // Asegúrate de pasar la variable
                'caja' => $caja,
                'totalIngresos' => $totalIngresos,
                'totalEgresos' => $totalEgresos,
                'saldoFinal' => $saldoFinal,
                'movimientos' => $caja->movimientos,
                'fecha_generacion' => now()->format('d/m/Y H:i')
            ]);

            return $pdf->stream("reporte-caja-{$id}.pdf");

        } catch (\Exception $e) {
            Log::error("Error al generar PDF de caja: " . $e->getMessage());
            return redirect()->route('admin.cajas.show', $id)
                ->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }

    // Función separada (puede estar en un trait o helper)
    private function numerosALetrasConDecimales($numero)
    {
        $formatter = new NumberFormatter("es", NumberFormatter::SPELLOUT);
        $partes = explode('.', number_format($numero, 2, '.', ''));
        $entero = $formatter->format($partes[0]);
        $decimal = $formatter->format($partes[1]);
        return ucfirst("$entero con $decimal/100");
    }
}
