<?php

namespace App\Http\Controllers;

use App\Models\Laboratorio;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Spatie\Permission\Traits\HasRoles;

use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use PDF;

class LaboratorioController extends Controller
{

    public function index()
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Laboratorios', 'url' => route('admin.laboratorios.index')],
        ];
        $laboratorios = Laboratorio::all();//LISTA DE USUARIOS
        return view('admin.laboratorios.index', compact('breadcrumb', 'laboratorios'));//ENVIARLOS DATOS ALA VISTA
    }


    public function create()
    {
        return view('admin.laboratorios.create');
    }


    public function store(Request $request)
    {
        // Validación de los datos de entrada
        $request->validate([

            'nombre' => 'required',
            'telefono' => 'required',
            'direccion' => 'required', //
        ]);

        // Crear un nuevo laboratorio
        $laboratorio = new Laboratorio();
        $laboratorio->nombre = $request->nombre;
        $laboratorio->telefono = $request->telefono;
        $laboratorio->sucursal_id = Auth::user()->sucursal_id;
        $laboratorio->direccion = $request->direccion;
        $laboratorio->save();

        $laboratorio->assignRole($request->role);//asignar un rol

        // Redirigir al índice con un mensaje de éxito
        return redirect()->route('admin.laboratorios.index')
            ->with('status', 'Laboratorio creada con éxito.');

    }


    public function show($id)
    {
        $laboratorio = Laboratorio::find($id); // buscar el laboratorio por ID

        return view('admin.laboratorios.show', compact('laboratorio')); // retornar vista de edición
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $laboratorio = Laboratorio::find($id); // buscar el laboratorio por ID

        return view('admin.laboratorios.edit', compact('laboratorio')); // retornar vista de edición
    }

    public function update(Request $request, $id)
    {
        //$datos =request()->all();
        //return response()->json($datos);
        // Validación de los datos de entrada
        $request->validate([
            'nombre' => 'required', // El nombre debe ser único, excepto para la categoría actual
            'telefono' => 'required',
            'direccion' => 'required',
        ]);

        // Buscar el laboratorio por ID
        $laboratorio = Laboratorio::find($id);

        // Actualizar los datos básicos
        $laboratorio->nombre = $request->nombre;
        $laboratorio->telefono = $request->telefono;
        $laboratorio->sucursal_id = Auth::user()->sucursal_id;
        $laboratorio->direccion = $request->direccion;

        $laboratorio->save();
        // Redirigir al índice con un mensaje de éxito
        return redirect()->route('admin.laboratorios.index')
            ->with('status', 'Se modifico la laboratorio');

    }

    public function destroy($id)
    {
        Laboratorio::destroy($id); // Buscar el usuario por ID


        // Redirigir al índice con un mensaje de éxito
        return redirect()->route('admin.laboratorios.index')
            ->with('status', 'laboratorio eliminada con éxito.');
    }
    public function listar()
    {
        return Laboratorio::select('id', 'nombre', 'telefono')->get();
    }

    public function generarReporte(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:pdf,excel,csv,print'
        ]);

        $laboratorios = Laboratorio::all();

        if ($laboratorios->isEmpty()) {
            return back()->with('error', 'No hay laboratorios para generar el reporte');
        }

        switch ($request->tipo) {
            case 'pdf':
                return $this->generarPDF($laboratorios);
            case 'excel':
                return $this->generarExcel($laboratorios);
            case 'csv':
                return $this->generarCSV($laboratorios);
            case 'print':
                return view('admin.laboratorios.reporte', [
                    'laboratorios' => $laboratorios,
                    'fecha_generacion' => now()->format('d/m/Y H:i:s')
                ]);
            default:
                abort(404);
        }
    }

    private function generarPDF($laboratorios)
    {
        $pdf = Pdf::loadView('admin.laboratorios.reporte', [
            'laboratorios' => $laboratorios,  // Cambiado para pasar la colección completa
            'fecha_generacion' => now()->format('d/m/Y H:i:s')
        ]);

        return $pdf->download('reporte_laboratorios_' . now()->format('YmdHis') . '.pdf');
    }

    private function generarExcel($laboratorios)
    {
        $data = $laboratorios->map(function ($laboratorio) {
            return [
                'Nombre' => $laboratorio->nombre,
                'Teléfono' => $laboratorio->telefono ?? 'No registrado', // Manejo de valores nulos
                'Dirección' => $laboratorio->direccion,
                'Fecha Registro' => $laboratorio->created_at->format('d/m/Y H:i') // Agregado campo de fecha
            ];
        });

        return Excel::download(
            new class ($data) implements
                \Maatwebsite\Excel\Concerns\FromCollection,
                \Maatwebsite\Excel\Concerns\WithHeadings,
                \Maatwebsite\Excel\Concerns\ShouldAutoSize,
                \Maatwebsite\Excel\Concerns\WithStyles {
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
                    'Nombre del Laboratorio',
                    'Teléfono de Contacto',
                    'Dirección',
                    'Fecha de Registro'
                ];
            }

            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
            {
                return [
                    // Estilo para los encabezados
                    1 => [
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF']
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '3498db'] // Azul profesional
                        ]
                    ],
                    // Centrar contenido en todas las celdas
                    'A:D' => [
                        'alignment' => [
                            'horizontal' => 'center',
                            'vertical' => 'center'
                        ]
                    ],
                    // Ajustar altura de filas
                    'A:D' => [
                        'rowHeight' => 25
                    ]
                ];
            }
            },
            'reporte_laboratorios_' . now()->format('YmdHis') . '.xlsx'
        );
    }

    private function generarCSV($laboratorios)
    {
        $data = $laboratorios->map(function ($laboratorio) {
            return [
                'Nombre' => $laboratorio->nombre,
                'Teléfono' => $laboratorio->telefono,
                'Dirección' => $laboratorio->direccion
                // Agrega más campos si es necesario
            ];
        });

        return Excel::download(
            new class ($data) implements FromCollection {
            private $data;
            public function __construct($data)
            {
                $this->data = $data;
            }
            public function collection()
            {
                return $this->data;
            }
            },
            'reporte_laboratorios_' . now()->format('YmdHis') . '.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }
}
