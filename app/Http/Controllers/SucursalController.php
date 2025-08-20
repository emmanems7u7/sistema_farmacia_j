<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;
use App\Models\User;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Carbon\Carbon;
use PDF;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class SucursalController extends Controller
{



    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        // Obtén todas las sucursales
        $sucursals = Sucursal::all();

        // Retorna la vista y envía la variable
        return view('admin.sucursals.index', compact('sucursals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sucursals.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //$datos =request()->all();
        //return response()->json($datos);

        $request->validate([
            'nombre' => 'required',
            'direccion' => 'required',
            'email' => 'required|unique:sucursals',
            'telefono' => 'required',
            'imagen' => 'required|image|mimes:jpg, jpeg,png',
        ]);
        //

        $sucursal = new Sucursal();
        $sucursal->nombre = $request->nombre;
        $sucursal->direccion = $request->direccion;
        $sucursal->email = $request->email;
        $sucursal->telefono = $request->telefono;
        $sucursal->imagen = $request->file('imagen')->store('imagenes', 'public');
        $sucursal->save();

        return redirect()->route('admin.sucursals.index')
            ->with('mensaje', 'SE CREO EL USUARIO');

    }


    /**
     * Display the specified resource.
     */
    public function show(Sucursal $sucursal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sucursal $sucursal)
    {
        $sucursal_id = Auth::user()->sucursal_id;
        $sucursal = Sucursal::where('id', $sucursal_id)->first();
        return view('admin.sucursals.edit', compact('sucursal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // $datos = request()->all();
        //return response()->json($datos);
        $request->validate([
            'nombre' => 'required',
            'direccion' => 'required',
            'email' => 'required',
            'telefono' => 'required',

        ]);


        $sucursal = Sucursal::find($id);
        $sucursal->nombre = $request->nombre;
        $sucursal->direccion = $request->direccion;
        $sucursal->email = $request->email;
        $sucursal->telefono = $request->telefono;
        //SI HAY UNA IMAGEN Q 
        if ($request->hasFile('imagen')) {
            //SE ELIMINA DE LA CARPETA
            Storage::delete('public/' . $sucursal->imagen);

            $sucursal->imagen = $request->file('imagen')->store('imagenes', 'public');
        }

        $sucursal->save();


        return redirect()->route('admin.sucursals.index')
            ->with('mensaje', 'SE MODIFICO LOS DATOS')
            ->with('icono', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy($id)
    {
        sucursal::destroy($id); // Buscar el usuario por ID
      

        // Redirigir al índice con un mensaje de éxito
        return redirect()->route('admin.sucursals.index')
            ->with('mensaje', 'sucursal eliminada con éxito.')
            ->with('icono', 'success');
    }



  public function generarReporte(Request $request) // Elimina el parámetro $tipo
{
    $request->validate([
        'tipo' => 'required|in:pdf,excel,csv,print'
    ]);
    
    $sucursals = Sucursal::all();
    return $this->generarReportePorTipo($request->tipo, $sucursals);
}
     protected function generarReportePorTipo($tipo, $sucursals)
    {
        switch ($tipo) {
            case 'pdf':
                return $this->generarPDF($sucursals);
            case 'excel':
                return $this->generarExcel($sucursals);
            case 'csv':
                return $this->generarCSV($sucursals);
            case 'print':
                return view('admin.sucursals.reporte', [
                    'sucursals' => $sucursals,
                    'fecha_generacion' => now()->format('d/m/Y H:i:s')
                ]);
            default:
                abort(404);
        }
    }

    private function generarPDF($sucursals)
    {
        $pdf = PDF::loadView('admin.sucursals.reporte', [
            'sucursals' => $sucursals,
            'fecha_generacion' => now()->format('d/m/Y H:i:s'),
            'page' => 1, 
            'pages' => 1
        ]);
        
        return $pdf->download('reporte_sucursales_'.now()->format('YmdHis').'.pdf');
    }

    private function generarExcel($sucursals)
    {
        $data = $sucursals->map(function ($sucursal) {
            return [
                'Nombre' => $sucursal->nombre,
                'Dirección' => $sucursal->direccion,
                'Email' => $sucursal->email,
                'Teléfono' => $sucursal->telefono,
                'Fecha Registro' => $sucursal->created_at->format('d/m/Y')
            ];
        });

        return Excel::download(
            new class($data) implements FromCollection {
                private $data;
                public function __construct($data) { $this->data = $data; }
                public function collection() { return $this->data; }
            },
            'reporte_sucursales_'.now()->format('YmdHis').'.xlsx'
        );
    }

    private function generarCSV($sucursals)
    {
        $data = $sucursals->map(function ($sucursal) {
            return [
                'Nombre' => $sucursal->nombre,
                'Dirección' => $sucursal->direccion,
                'Email' => $sucursal->email,
                'Teléfono' => $sucursal->telefono,
                'Fecha Registro' => $sucursal->created_at->format('d/m/Y')
            ];
        });

        return Excel::download(
            new class($data) implements FromCollection {
                private $data;
                public function __construct($data) { $this->data = $data; }
                public function collection() { return $this->data; }
            },
            'reporte_sucursales_'.now()->format('YmdHis').'.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }
}