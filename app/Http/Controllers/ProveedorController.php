<?php

namespace App\Http\Controllers;
use App\Models\Sucursal;
use App\Models\Proveedor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use PDF;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Proveedores', 'url' => route('admin.laboratorios.index')],


        ];
        $proveedores = Proveedor::all();
        return view('admin.proveedores.index', compact('breadcrumb', 'proveedores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //$datos =request()->all();
        //return response()->json($datos);

        // Validación de los datos de entrada
        $request->validate([
            'empresa' => 'required',
            'direccion' => 'required',
            'telefono' => 'required',
            'email' => 'required',
            'nombre' => 'required',
            'celular' => 'required',

        ]);

        // Crear un nuevo proveedor
        $proveedor = new Proveedor();
        $proveedor->empresa = $request->empresa;
        $proveedor->direccion = $request->direccion;
        $proveedor->telefono = $request->telefono;
        $proveedor->email = $request->email;
        $proveedor->nombre = $request->nombre;
        $proveedor->celular = $request->celular;
        $proveedor->sucursal_id = Auth::user()->sucursal_id;
        $proveedor->save();

        // $proveedor->assignRole($request->role);//asignar un rol

        // Redirigir al índice con un mensaje de éxito
        return redirect()->route('admin.proveedores.index')
            ->with('status', 'Se registro al proveedor  con éxito.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Proveedor $proveedor)
    {
        $proveedor = Proveedor::find($id); // buscar el proveedor por ID

        return view('admin.proveedores.show', compact('proveedor')); // retornar vista de edición
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $proveedor = Proveedor::find($id); // buscar el proveedor por ID

        return view('admin.proveedores.edit', compact('proveedor')); // retornar vista de edición

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        // Validación de los datos de entrada
        $request->validate([
            'empresa' => 'required',
            'direccion' => 'required',
            'telefono' => 'required',
            'email' => 'required',
            'nombre' => 'required',
            'celular' => 'required',

        ]);

        //  un nuevo proveedor
        $proveedor = Proveedor::find($id);
        $proveedor->empresa = $request->empresa;
        $proveedor->direccion = $request->direccion;
        $proveedor->telefono = $request->telefono;
        $proveedor->email = $request->email;
        $proveedor->nombre = $request->nombre;
        $proveedor->celular = $request->celular;
        $proveedor->sucursal_id = Auth::user()->sucursal_id;
        $proveedor->save();

        $proveedor->assignRole($request->role);//asignar un rol

        // Redirigir al índice con un mensaje de éxito
        return redirect()->route('admin.proveedores.index')
            ->with('status', 'Se modifico al proveedor  con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Proveedor::destroy($id); // Buscar el usuario por ID


        // Redirigir al índice con un mensaje de éxito
        return redirect()->route('admin.proveedores.index')
            ->with('status', 'se elimino al proveedor de manera correcta.');
    }

    public function generarReporte(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:pdf,excel,csv,print'
        ]);

        $proveedores = Proveedor::all(); // Cambiado a plural

        if ($proveedores->isEmpty()) {
            return back()->with('error', 'No hay proveedores para generar el reporte');
        }

        switch ($request->tipo) {
            case 'pdf':
                return $this->generarPDF($proveedores);
            case 'excel':
                return $this->generarExcel($proveedores);
            case 'csv':
                return $this->generarCSV($proveedores);
            case 'print':
                return view('admin.proveedores.reporte', [
                    'proveedores' => $proveedores, // Cambiado a plural
                    'fecha_generacion' => now()->format('d/m/Y H:i:s')
                ]);
            default:
                abort(404);
        }
    }

    private function generarPDF($proveedores)
    {
        $pdf = Pdf::loadView('admin.proveedores.reporte', [
            'proveedores' => $proveedores, // Cambiado a plural
            'fecha_generacion' => now()->format('d/m/Y H:i:s')
        ]);

        return $pdf->download('reporte_proveedores_' . now()->format('YmdHis') . '.pdf');
    }

    private function generarExcel($proveedores)
    {
        $data = $proveedores->map(function ($proveedor) {
            return [
                'Empresa' => $proveedor->nombre,
                'Dirección' => $proveedor->direccion ?? 'No especificada',
                'Teléfono' => $proveedor->telefono ?? 'No registrado',
                'Email' => $proveedor->email ?? 'Sin email',
                'Contacto' => $proveedor->nombre_contacto ?? $proveedor->nombre, // Usa nombre_contacto o nombre como fallback
                'Celular' => $proveedor->celular ?? 'No registrado'
            ];
        });

        return Excel::download(
            new class ($data) implements
                \Maatwebsite\Excel\Concerns\FromCollection,
                \Maatwebsite\Excel\Concerns\WithHeadings,
                \Maatwebsite\Excel\Concerns\WithStyles,
                \Maatwebsite\Excel\Concerns\ShouldAutoSize {

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
                    'Empresa',
                    'Dirección',
                    'Teléfono Principal',
                    'Email',
                    'Contacto',
                    'Teléfono Móvil'
                ];
            }

            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
            {
                return [
                    // Estilo encabezados
                    1 => [
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => 'center']
                    ],
                    // Estilo cuerpo
                    'A2:F' . $sheet->getHighestRow() => [
                        'alignment' => ['vertical' => 'center']
                    ],
                    // Alineación izquierda para empresa y dirección
                    'A2:B' . $sheet->getHighestRow() => [
                        'alignment' => ['horizontal' => 'left']
                    ],
                    // Wrap text para dirección y email
                    'B2:D' . $sheet->getHighestRow() => [
                        'alignment' => ['wrapText' => true]
                    ]
                ];
            }
            },
            'reporte_proveedores_' . now()->format('YmdHis') . '.xlsx'
        );
    }

    private function generarCSV($proveedores)
    {
        $data = $proveedores->map(function ($proveedor) {
            return [
                'Empresa' => $proveedor->nombre,
                'Dirección' => $proveedor->direccion,
                'Teléfono' => $proveedor->telefono,
                'Email' => $proveedor->email,
                'Nombre' => $proveedor->nombre,
                'Celular' => $proveedor->celular
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
            'reporte_proveedores_' . now()->format('YmdHis') . '.csv', // Corregido nombre
            \Maatwebsite\Excel\Excel::CSV
        );
    }
}
