<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use PDF;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Clientes', 'url' => route('admin.clientes.index')],
        ];
        $clientes = Cliente::all();
        return view('admin.clientes.index', compact('clientes', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        //
        // Validación de los datos de entrada
        $request->validate([

            'nombre_cliente' => 'required',
            'nit_ci' => 'nullable',
            'celular' => 'nullable',
            'email' => 'nullable', //
        ]);

        // Crear un nuevo cliente
        $cliente = new Cliente();
        $cliente->nombre_cliente = $request->nombre_cliente;
        $cliente->nit_ci = $request->nit_ci;
        $cliente->celular = $request->celular;
        $cliente->email = $request->email;
        $cliente->sucursal_id = Auth::user()->sucursal_id;
        $cliente->save();

        $cliente->assignRole($request->role);//asignar un rol

        // Redirigir al índice con un mensaje de éxito
        return redirect()->route('admin.clientes.index')
            ->with('status', 'Cliente creado con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        //
        $cliente = Cliente::find($id); // buscar el cliente por ID

        return view('admin.clientes.show', compact('cliente')); // retornar vista de edició
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $cliente = Cliente::find($id); // buscar el cliente por ID

        return view('admin.clientes.edit', compact('cliente')); // retornar vista de edición
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        //$datos =request()->all();
        //return response()->json($datos);
        // Validación de los datos de entrada
        $request->validate([
            'nombre_cliente' => 'required',
            'nit_ci' => 'nullable',
            'celular' => 'nullable',
            'email' => 'nullable',
        ]);

        // Buscar el cliente por ID
        $cliente = Cliente::find($id);

        // Actualizar los datos básicos
        $cliente->nombre_cliente = $request->nombre_cliente;
        $cliente->nit_ci = $request->nit_ci;
        $cliente->celular = $request->celular;
        $cliente->email = $request->email;
        $cliente->sucursal_id = Auth::user()->sucursal_id;


        $cliente->save();
        // Redirigir al índice con un mensaje de éxito
        return redirect()->route('admin.clientes.index')
            ->with('status', 'Se modifico la cliente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Cliente::destroy($id); // Buscar el usuario por ID


        // Redirigir al índice con un mensaje de éxito
        return redirect()->route('admin.clientes.index')
            ->with('status', 'Cliente eliminada con éxito.');
    }
    public function generarReporte(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:pdf,excel,csv,print'
        ]);

        $clientes = Cliente::all();

        if ($clientes->isEmpty()) {
            return back()->with('error', 'No hay clientes para generar el reporte');
        }

        switch ($request->tipo) {
            case 'pdf':
                return $this->generarPDF($clientes);
            case 'excel':
                return $this->generarExcel($clientes);
            case 'csv':
                return $this->generarCSV($clientes);
            case 'print':
                return view('admin.clientes.reporte', [
                    'clientes' => $clientes,
                    'fecha_generacion' => now()->format('d/m/Y H:i:s')
                ]);
            default:
                abort(404);
        }
    }

    private function generarPDF($clientes)
    {
        $pdf = Pdf::loadView('admin.clientes.reporte', [
            'clientes' => $clientes,
            'fecha_generacion' => now()->format('d/m/Y H:i:s')
        ]);

        return $pdf->download('reporte_clientes_' . now()->format('YmdHis') . '.pdf');
    }

    private function generarExcel($clientes)
    {
        $data = $clientes->map(function ($cliente) {
            return [
                'ID' => $cliente->id,
                'Nombre del Cliente' => $cliente->nombre_cliente,
                'NIT/CI' => $cliente->nit_ci ?? 'N/A',
                'Celular' => $cliente->celular ?? 'No registrado',
                'Email' => $cliente->email ?? 'Sin email',
                'Fecha Registro' => $cliente->created_at->format('d/m/Y H:i'),
                'Última Actualización' => $cliente->updated_at->format('d/m/Y H:i')
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
                    'Nombre del Cliente',
                    'NIT/CI',
                    'Celular',
                    'Email',
                    'Fecha de Registro',
                    'Última Actualización'
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
                            'startColor' => ['rgb' => '2C3E50'] // Azul oscuro
                        ],
                        'alignment' => [
                            'horizontal' => 'center',
                            'vertical' => 'center'
                        ]
                    ],
                    // Estilo cuerpo
                    'A2:G' . $sheet->getHighestRow() => [
                        'alignment' => [
                            'vertical' => 'center',
                            'wrapText' => true
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['rgb' => 'EEEEEE']
                            ]
                        ]
                    ],
                    // Alineación izquierda para nombres
                    'B2:B' . $sheet->getHighestRow() => [
                        'alignment' => [
                            'horizontal' => 'left'
                        ]
                    ],
                    // Formato condicional para emails válidos
                    'E2:E' . $sheet->getHighestRow() => [
                        'font' => [
                            'color' => ['rgb' => '27AE60'] // Verde si tiene email
                        ]
                    ]
                ];
            }

            public function columnWidths(): array
            {
                return [
                    'A' => 10,  // ID
                    'B' => 30,  // Nombre
                    'C' => 15,  // NIT/CI
                    'D' => 15,  // Celular
                    'E' => 25,  // Email
                    'F' => 20,  // Fecha Registro
                    'G' => 20   // Actualización
                ];
            }
            },
            'reporte_clientes_' . now()->format('YmdHis') . '.xlsx'
        );
    }
    private function generarCSV($clientes)
    {
        $data = $clientes->map(function ($cliente) {
            return [
                'Nombre_cliente' => $cliente->nombre_cliente,
                'Nit_ci' => $cliente->nit_ci,
                'Celular' => $cliente->celular,
                'Email' => $cliente->email
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
            'reporte_clientes_' . now()->format('YmdHis') . '.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }
}
