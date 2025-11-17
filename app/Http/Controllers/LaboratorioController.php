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
        $laboratorios = Laboratorio::all();
        return view('admin.laboratorios.index', compact('breadcrumb', 'laboratorios'));
    }


    public function create()
    {
        return view('admin.laboratorios.create');
    }


    public function store(Request $request)
    {
        // Validación de los datos de entrada
        $request->validate([

        'nombre' => 'required|string|max:255',
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'nullable|string|max:255',
        'nit' => 'nullable|string|max:20',
        'correo' => 'nullable|email|max:255',
        'nombre_proveedor' => 'nullable|string|max:255',
        'celular' => 'nullable|string|max:20',
        ]);

        // Crear un nuevo laboratorio
        $laboratorio = new Laboratorio();
        $laboratorio->nombre = $request->nombre;
        $laboratorio->telefono = $request->telefono;
        $laboratorio->sucursal_id = Auth::user()->sucursal_id;
        $laboratorio->direccion = $request->direccion;
        $laboratorio->nit = $request->nit;
        $laboratorio->correo = $request->correo;
        $laboratorio->nombre_proveedor = $request->nombre_proveedor;
        $laboratorio->celular = $request->celular;
        
        $laboratorio->save();

        $laboratorio->assignRole($request->role);//asignar un rol

        
        return redirect()->route('admin.laboratorios.index')
            ->with('status', 'Laboratorio creada con éxito.');

    }


    public function show($id)
    {
        $laboratorio = Laboratorio::find($id); 

        return view('admin.laboratorios.show', compact('laboratorio')); 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $laboratorio = Laboratorio::find($id); 

        return view('admin.laboratorios.edit', compact('laboratorio')); 
    }

    public function update(Request $request, $id)
    {
        //$datos =request()->all();
        //return response()->json($datos);
        // Validación de los datos de entrada
        $request->validate([
            

             'nombre' => 'required|string|max:255',
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'nullable|string|max:255',
        'nit' => 'nullable|string|max:20',
        'correo' => 'nullable|email|max:255',
        'nombre_proveedor' => 'nullable|string|max:255',
        'celular' => 'nullable|string|max:20',
        ]);

        // Buscar el laboratorio por ID
        $laboratorio = Laboratorio::find($id);

        // Actualizar los datos básicos

 $laboratorio->nombre = $request->nombre;
    $laboratorio->telefono = $request->telefono;
    $laboratorio->sucursal_id = Auth::user()->sucursal_id;
    $laboratorio->direccion = $request->direccion;
    $laboratorio->nit = $request->nit;
    $laboratorio->correo = $request->correo;
    $laboratorio->nombre_proveedor = $request->nombre_proveedor;
    $laboratorio->celular = $request->celular;

        $laboratorio->save();
        
        return redirect()->route('admin.laboratorios.index')
            ->with('status', 'Se modifico la laboratorio');

    }

    public function destroy($id)
    {
        Laboratorio::destroy($id); 


        
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
            'laboratorios' => $laboratorios,  
            'fecha_generacion' => now()->format('d/m/Y H:i:s')
        ]);

        return $pdf->download('reporte_laboratorios_' . now()->format('YmdHis') . '.pdf');
    }

    private function generarExcel($laboratorios)
    {
        $data = $laboratorios->map(function ($laboratorio) {
            return [
                'Nombre' => $laboratorio->nombre,
                'Teléfono' => $laboratorio->telefono ?? 'No registrado', 
                'Dirección' => $laboratorio->direccion,
                'Fecha Registro' => $laboratorio->created_at->format('d/m/Y H:i') 
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
                    
                    1 => [
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF']
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '3498db'] 
                        ]
                    ],
                  
                    'A:D' => [
                        'alignment' => [
                            'horizontal' => 'center',
                            'vertical' => 'center'
                        ]
                    ],
              
                    'A:D' => [
                        'rowHeight' => 25
                    ]
                ];
            }
            },
            'reporte_laboratorios_' . now()->format('YmdHis') . '.xlsx'
        );
    }

    
}
