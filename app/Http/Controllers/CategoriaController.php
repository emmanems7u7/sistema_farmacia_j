<?php

namespace App\Http\Controllers;

use App\Models\Categoria;

use App\Models\Sucursal;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use PDF;




class CategoriaController extends Controller
{

    public function index(Request $request)
    {

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Catalogo', 'url' => route('catalogos.index')],


        ];

        // Obtener todas las sucursales para mostrar en un select
        $sucursales = Sucursal::all();

        // Filtrar categorías por sucursal si se envía el parámetro
        $query = Categoria::query();

        if ($request->has('sucursal_id')) {
            $query->whereHas('sucursales', function ($q) use ($request) {
                $q->where('sucursales.id', $request->sucursal_id);
            });
        }

        $categorias = $query->get();

        return view('admin.categorias.index', compact('breadcrumb', 'categorias', 'sucursales'));
    }

    public function create()
    {
        return view('admin.categorias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de los datos de entrada
        $request->validate([

            'nombre' => 'required|unique:categorias',
            'descripcion' => 'required', //
        ]);

        // Crear un nuevo categoria
        $categoria = new Categoria();
        $categoria->nombre = $request->nombre;
        $categoria->sucursal_id = Auth::user()->sucursal_id;
        $categoria->descripcion = $request->descripcion;
        $categoria->save();

        // $categoria->assignRole($request->role);//asignar un rol

        // Redirigir al índice con un mensaje de éxito
        return redirect()->back()->with('status', 'Categoria creada con éxito.');

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $categoria = Categoria::find($id); // buscar el categoria por ID

        return view('admin.categorias.show', compact('categoria')); // retornar vista de edición
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $categoria = Categoria::find($id); // buscar el categoria por ID

        return view('admin.categorias.edit', compact('categoria')); // retornar vista de edición
    }

    public function update(Request $request, $id)
    {
        //$datos =request()->all();
        //return response()->json($datos);
        // Validación de los datos de entrada
        $request->validate([
            'nombre' => 'required|unique:categorias,nombre,' . $id, // El nombre debe ser único, excepto para la categoría actual
            'descripcion' => 'required',
        ]);

        // Buscar el categoria por ID
        $categoria = Categoria::find($id);

        // Actualizar los datos básicos
        $categoria->nombre = $request->nombre;
        $categoria->sucursal_id = Auth::user()->sucursal_id;
        $categoria->descripcion = $request->descripcion;

        $categoria->save();
        // Redirigir al índice con un mensaje de éxito
        return redirect()->back()
            ->with('status', 'Se modifico la categoria');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Categoria::destroy($id); // Buscar el usuario por ID


        // Redirigir al índice con un mensaje de éxito
        return redirect()->back()
            ->with('status', 'categoria eliminada con éxito.');
    }


    public function generarReporte(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:pdf,excel,csv,print'
        ]);

        $categorias = Categoria::all(); // Obtener todas las categorías

        if ($categorias->isEmpty()) {
            return back()->with('error', 'No hay categorías para generar el reporte');
        }

        return $this->generarReportePorTipo($request->tipo, $categorias);
    }

    protected function generarReportePorTipo($tipo, $categorias)
    {
        $data = [
            'categorias' => $categorias,
            'fecha_generacion' => now()->format('d/m/Y H:i:s')
        ];

        switch ($tipo) {
            case 'pdf':
                $pdf = PDF::loadView('admin.categorias.reporte', $data);
                return $pdf->download('reporte_categorias_' . now()->format('Ymd_His') . '.pdf');

            case 'excel':
                return $this->generarExcel($categorias);

            case 'csv':
                return $this->generarCSV($categorias);

            case 'print':
                return view('admin.categorias.reporte', $data);

            default:
                abort(404);
        }
    }

    private function generarPDF($categorias, $sucursalId = null)
    {
        // Asegurarse de tener valores por defecto
        $sucursalNombre = 'Todas las sucursales';

        if ($sucursalId) {
            $sucursal = Sucursal::find($sucursalId);
            $sucursalNombre = $sucursal ? $sucursal->nombre : 'Sucursal seleccionada';
        }

        $pdf = PDF::loadView('admin.categorias.reporte', [
            'categorias' => $categorias,
            'fecha_generacion' => now()->format('d/m/Y H:i:s'),
            'sucursalId' => $sucursalId, // Asegúrate de pasar este valor
            'sucursalNombre' => $sucursalNombre // Y este también
        ])->setPaper('a4', 'portrait');

        $nombreArchivo = 'reporte_categorias_';
        $nombreArchivo .= $sucursalId ? 'sucursal_' . $sucursalId . '_' : '';
        $nombreArchivo .= now()->format('Ymd_His') . '.pdf';

        return $pdf->download($nombreArchivo);
    }


    private function generarExcel($categorias)
    {
        $data = $categorias->map(function ($categoria) {
            return [
                'ID' => $categoria->id,
                'Nombre' => $categoria->nombre,
                'Descripción' => $categoria->descripcion ?? 'Sin descripción',
                'N° Productos' => $categoria->productos_count ?? $categoria->productos->count(),
                'Fecha Creación' => $categoria->created_at->format('d/m/Y H:i')

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
                    'ID',
                    'Nombre Categoría',
                    'Descripción',
                    'N° Productos',
                    'Fecha Creación'

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
                    'A2:F' . $sheet->getHighestRow() => [
                        'alignment' => [
                            'vertical' => 'center',
                            'wrapText' => true
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['rgb' => 'DDDDDD']
                            ]
                        ]
                    ],
                    // Alineación izquierda para nombre y descripción
                    'B2:C' . $sheet->getHighestRow() => [
                        'alignment' => [
                            'horizontal' => 'left'
                        ]
                    ],

                ];
            }

            public function columnWidths(): array
            {
                return [
                    'A' => 8,   // ID
                    'B' => 25,  // Nombre
                    'C' => 35,  // Descripción
                    'D' => 12,  // N° Productos
                    'E' => 18  // Fecha

                ];
            }
            },
            'reporte_categorias_' . now()->format('YmdHis') . '.xlsx'
        );
    }

    private function generarCSV($categorias)
    {
        $filename = 'reporte_categorias_' . now()->format('YmdHis') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($categorias) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nombre', 'Descripción']); // Encabezados
            foreach ($categorias as $categoria) {
                fputcsv($file, [$categoria->nombre, $categoria->descripcion]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
