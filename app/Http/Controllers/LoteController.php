<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lote;
use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Carbon\Carbon;

class LoteController extends Controller
{
    public function index(Request $request)
    {

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Lotes', 'url' => route('admin.lotes.index')],
        ];

        // Inicializar query
        $query = Lote::query();

        // Filtros
        if ($request->has('producto_id') && $request->producto_id != '') {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->has('estado')) {
            switch ($request->estado) {
                case 'activos':
                    $query->where('activo', true)
                        ->where('cantidad', '>', 0)
                        ->where(function ($q) {
                            $q->whereNull('fecha_vencimiento')
                                ->orWhere('fecha_vencimiento', '>=', now());
                        });
                    break;

                case 'inactivos':
                    $query->where('activo', false);
                    break;

                case 'vencidos':
                    $query->where(function ($q) {
                        $q->where('fecha_vencimiento', '<', now())
                            ->orWhere('cantidad', '<=', 0);
                    });
                    break;

                case 'agotados':
                    $query->where('cantidad', '<=', 0);
                    break;

                case 'multiples':
                    $query->whereIn('producto_id', function ($q) {
                        $q->select('producto_id')
                            ->from('lotes')
                            ->where('activo', true)
                            ->groupBy('producto_id')
                            ->havingRaw('COUNT(*) > 1');
                    });
                    break;
            }
        }

        $lotes = $query->orderBy('fecha_vencimiento', 'asc')->get();
        $productos = Producto::all();
        $totalLotes = Lote::count();

        return view('admin.lotes.index', compact('breadcrumb', 'lotes', 'productos', 'totalLotes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'numero_lote' => 'required|string|unique:lotes',
            'cantidad' => 'required|integer|min:1',
            'fecha_vencimiento' => 'nullable|date',
        ]);

        Lote::create($request->all());

        return redirect()->route('productos.show', $request->producto_id)
            ->with('success', 'Lote agregado correctamente');
    }

    public function generarReporte(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:pdf,excel,csv,print'
        ]);

        $lotes = Lote::with('producto')->orderBy('fecha_vencimiento', 'asc')->get()
            ->map(function ($lote) {
                // Procesamiento seguro de fechas
                $lote->fecha_ingreso_formatted = $lote->fecha_ingreso
                    ? Carbon::parse($lote->fecha_ingreso)->format('d/m/Y')
                    : 'N/A';

                $lote->fecha_vencimiento_formatted = $lote->fecha_vencimiento
                    ? Carbon::parse($lote->fecha_vencimiento)->format('d/m/Y')
                    : 'N/A';

                $lote->dias_restantes = $lote->fecha_vencimiento
                    ? now()->diffInDays(Carbon::parse($lote->fecha_vencimiento), false) * -1
                    : 'N/A';

                $lote->estado = $lote->fecha_vencimiento
                    ? (Carbon::parse($lote->fecha_vencimiento) < now() ? 'VENCIDO' : 'VIGENTE')
                    : 'SIN FECHA';

                return $lote;
            });

        if ($lotes->isEmpty()) {
            return back()->with('error', 'No hay lotes para generar el reporte');
        }

        switch ($request->tipo) {
            case 'pdf':
                return $this->generarPDF($lotes);
            case 'excel':
                return $this->generarExcel($lotes);
            case 'csv':
                return $this->generarCSV($lotes);
            case 'print':
                return view('admin.lotes.reporte', [
                    'lotes' => $lotes,
                    'fecha_generacion' => now()->format('d/m/Y H:i:s')
                ]);
            default:
                abort(404);
        }
    }

    private function generarPDF($lotes)
    {
        $pdf = PDF::loadView('admin.lotes.reporte', [
            'lotes' => $lotes,
            'fecha_generacion' => now()->format('d/m/Y H:i:s')
        ]);

        return $pdf->download('reporte_lotes_' . now()->format('YmdHis') . '.pdf');
    }

    private function generarExcel($lotes)
    {
        $data = $lotes->map(function ($lote) {
            return [
                'Producto' => $lote->producto->nombre ?? 'N/A',
                'Código' => $lote->producto->codigo ?? 'N/A',
                'Lote' => $lote->numero_lote,
                'Cantidad' => $lote->cantidad,
                'P. Compra' => number_format($lote->precio_compra, 2),
                'P. Venta' => number_format($lote->precio_venta, 2),
                'Ingreso' => $lote->fecha_ingreso_formatted,
                'Vencimiento' => $lote->fecha_vencimiento_formatted,
                'Días Rest.' => is_numeric($lote->dias_restantes) ? max(0, $lote->dias_restantes) : $lote->dias_restantes
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
                    'Producto',
                    'Código',
                    'N° Lote',
                    'Cantidad',
                    'Precio Compra',
                    'Precio Venta',
                    'Fecha Ingreso',
                    'Fecha Vencimiento',
                    'Días Restantes'
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
                    'A2:I' . $sheet->getHighestRow() => [
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
                    // Alineación izquierda para nombres
                    'A2:B' . $sheet->getHighestRow() => [
                        'alignment' => [
                            'horizontal' => 'left'
                        ]
                    ]
                ];
            }

            public function columnWidths(): array
            {
                return [
                    'A' => 30,  // Producto
                    'B' => 15,  // Código
                    'C' => 15,  // Lote
                    'D' => 12,  // Cantidad
                    'E' => 15,  // P. Compra
                    'F' => 15,  // P. Venta
                    'G' => 15,  // Ingreso
                    'H' => 15,  // Vencimiento
                    'I' => 12   // Días Rest.
                ];
            }
            },
            'reporte_lotes_' . now()->format('YmdHis') . '.xlsx'
        );
    }

    private function generarCSV($lotes)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reporte_lotes_' . now()->format('YmdHis') . '.csv"',
        ];

        $callback = function () use ($lotes) {
            $file = fopen('php://output', 'w');

            // Encabezados
            fputcsv($file, [
                'Producto',
                'Código',
                'Lote',
                'Cantidad',
                'P. Compra',
                'P. Venta',
                'Ingreso',
                'Vencimiento',
                'Días Rest.',
                'Estado'
            ]);

            // Datos
            foreach ($lotes as $lote) {
                fputcsv($file, [
                    $lote->producto->nombre ?? 'N/A',
                    $lote->producto->codigo ?? 'N/A',
                    $lote->numero_lote,
                    $lote->cantidad,
                    number_format($lote->precio_compra, 2),
                    number_format($lote->precio_venta, 2),
                    $lote->fecha_ingreso_formatted,
                    $lote->fecha_vencimiento_formatted,
                    is_numeric($lote->dias_restantes) ? max(0, $lote->dias_restantes) : $lote->dias_restantes,
                    $lote->estado
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // Métodos básicos (puedes implementarlos según necesites)
    public function create()
    { /* ... */
    }
    public function show($id)
    { /* ... */
    }
    public function edit($id)
    { /* ... */
    }
    public function update(Request $request, $id)
    { /* ... */
    }
    public function destroy($id)
    { /* ... */
    }
}