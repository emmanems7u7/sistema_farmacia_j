<?php



namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Laboratorio;
use App\Models\Lote;
use Illuminate\Support\Facades\Auth;
use App\Models\Producto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Str;
class ProductoController extends Controller
{
    public function index()
    {

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Productos', 'url' => route('admin.laboratorios.index')],
        ];
        $productos = Producto::with(['categoria', 'laboratorio', 'lotes'])->get();
        $categorias = Categoria::all();
        $laboratorios = Laboratorio::all();

        return view('admin.productos.index', compact('breadcrumb', 'productos', 'categorias', 'laboratorios'));
    }

    public function create()
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Productos', 'url' => route('admin.productos.index')],
        ];
        $laboratorios = Laboratorio::all();
        $categorias = Categoria::all();
        return view('admin.productos.create', compact('breadcrumb', 'categorias', 'laboratorios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|unique:productos,codigo',
            'nombre' => 'required',
            'stock_minimo' => 'required',
            'stock_maximo' => 'required',
            'descripcion' => 'required',
            'imagen' => 'required|image|mimes:jpg,jpeg,png',
        ]);

        $producto = new Producto();
        $producto->codigo = $request->codigo;
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->stock_minimo = $request->stock_minimo;
        $producto->stock_maximo = $request->stock_maximo;
        $producto->categoria_id = $request->categoria_id;
        $producto->laboratorio_id = $request->laboratorio_id;
        $producto->sucursal_id = 1;

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            // Guardar en /public/storage/productos
            $file->move(public_path('storage/productos'), $filename);

            // Guardar la ruta relativa en la BD
            $producto->imagen = 'productos/' . $filename;
        }

        $producto->save();

        return redirect()->route('admin.productos.index')
            ->with('status', 'Se registró el producto');
    }

    public function show(string $id)
    {
        $producto = Producto::with('lotes')->findOrFail($id);
        return view('admin.productos.show', compact('producto'));
    }

    public function edit($id)
    {
        $producto = Producto::find($id);
        $categorias = Categoria::all();
        $laboratorios = Laboratorio::all();
        return view('admin.productos.edit', compact('producto', 'categorias', 'laboratorios'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'codigo' => 'required|unique:productos,codigo,' . $id,
            'nombre' => 'required',
            'stock_minimo' => 'required|integer',
            'stock_maximo' => 'required|integer',
            'descripcion' => 'required',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'fecha_ingreso' => 'required|date',
            'fecha_vencimiento' => 'nullable|date',
            'cantidad' => 'required|integer|min:1'
        ]);

        // Actualizar el producto
        $producto = Producto::findOrFail($id);

        $producto->update([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'stock_minimo' => $request->stock_minimo,
            'stock_maximo' => $request->stock_maximo,
            'categoria_id' => $request->categoria_id,
            'laboratorio_id' => $request->laboratorio_id
        ]);

        // Manejar la imagen si se subió una nueva
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->imagen && file_exists(public_path('storage/' . $producto->imagen))) {
                unlink(public_path('storage/' . $producto->imagen));
            }

            $file = $request->file('imagen');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/productos'), $filename);
            $producto->imagen = 'productos/' . $filename;
        }
        // Guardar cambios en producto (incluye imagen)
        $producto->save();

        // Manejar el lote del producto
        $loteData = [
            'precio_compra' => $request->precio_compra,
            'precio_venta' => $request->precio_venta,
            'fecha_ingreso' => $request->fecha_ingreso,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'cantidad' => $request->cantidad,
            'cantidad_inicial' => 1
        ];

        if ($request->has('lote_id') && $request->lote_id) {
            // Actualizar lote existente
            $lote = Lote::findOrFail($request->lote_id);
            $lote->update($loteData);
        } else {
            // Crear nuevo lote
            $loteData['producto_id'] = $producto->id;
            $loteData['numero_lote'] = 'LOTE-' . strtoupper(Str::random(6));
            Lote::create($loteData);
        }

        return redirect()->route('admin.productos.index')
            ->with('status', 'Producto actualizado correctamente');
    }

    public function destroy($id)
    {
        $producto = Producto::find($id);

        if ($producto->imagen && file_exists(public_path('storage/productos/' . basename($producto->imagen)))) {
            unlink(public_path('storage/productos/' . basename($producto->imagen)));
        }

        // Eliminar los lotes asociados primero
        $producto->lotes()->delete();
        $producto->delete();

        return redirect()->route('admin.productos.index')
            ->with('status', 'Se eliminó con éxito.');
    }



    public function buscar(Request $request)
    {
        $term = $request->input('term');

        $productos = Producto::where('codigo', 'like', "%$term%")
            ->orWhere('nombre', 'like', "%$term%")
            ->select('id', 'codigo', 'nombre', 'descripcion')
            ->limit(10)
            ->get();

        return response()->json($productos);
    }

    public function get($id)
    {
        $producto = Producto::find($id);

        if ($producto) {
            return response()->json([
                'success' => true,
                'producto' => $producto
            ]);
        }

        return response()->json(['success' => false]);
    }


    public function generarReporte($tipo, Request $request)
    {
        // Validación del tipo de reporte
        if (!in_array($tipo, ['pdf', 'excel', 'csv'])) {
            abort(400, 'Tipo de reporte no válido');
        }

        // Obtener y procesar filtros
        $filtros = $this->procesarFiltros($request);

        // Obtener productos con filtros aplicados
        $productos = $this->obtenerProductosFiltrados($filtros);

        // Verificar si hay datos
        if ($productos->isEmpty()) {
            return back()->with('error', 'No hay productos con los filtros seleccionados');
        }

        // Convertir fechas a objetos Carbon
        $productos = $this->convertirFechas($productos);

        // Generar el reporte según el tipo
        return $this->generarReportePorTipo($tipo, $productos);
    }

    protected function procesarFiltros(Request $request): array
    {
        return [
            'categoria_id' => $request->input('categoria'),
            'stockBajo' => $request->input('stockBajo', 0),
            'diasVencimiento' => $request->input('diasVencimiento')
        ];
    }

    protected function obtenerProductosFiltrados(array $filtros)
    {
        $query = Producto::with('categoria', 'laboratorio')
            ->where('sucursal_id', Auth::user()->sucursal_id);

        if ($filtros['categoria_id']) {
            $query->where('categoria_id', $filtros['categoria_id']);
        }

        if ($filtros['stockBajo']) {
            $query->where('stock', '<', \DB::raw('stock_minimo'));
        }

        if ($filtros['diasVencimiento']) {
            $fechaVencimiento = now()->addDays($filtros['diasVencimiento']);
            $query->whereDate('fecha_vencimiento', '<=', $fechaVencimiento)
                ->whereNotNull('fecha_vencimiento');
        }

        return $query->get();
    }

    protected function convertirFechas($productos)
    {
        return $productos->map(function ($producto) {
            $producto->fecha_ingreso = Carbon::parse($producto->fecha_ingreso);
            $producto->fecha_vencimiento = $producto->fecha_vencimiento
                ? Carbon::parse($producto->fecha_vencimiento)
                : null;
            return $producto;
        });
    }

    protected function generarReportePorTipo($tipo, $productos)
    {
        switch ($tipo) {
            case 'pdf':
                return $this->generarPDF($productos);
            case 'excel':
                return $this->generarExcel($productos);
            case 'csv':
                return $this->generarCSV($productos);
        }
    }

    private function generarPDF($productos)
    {
        $pdf = PDF::loadView('admin.productos.reporte', [
            'productos' => $productos,
            'fecha_generacion' => now()->format('d/m/Y H:i:s'),
            'page' => 1,
            'pages' => 1
        ]);

        return $pdf->download('reporte_productos_' . now()->format('YmdHis') . '.pdf');
    }

    private function generarExcel($productos)
    {
        $data = $productos->map(function ($producto) {
            // Si el producto tiene lotes, usamos los datos del primer lote
            if ($producto->lotes->isNotEmpty()) {
                $lote = $producto->lotes->first();
                return [
                    'Código' => $producto->codigo,
                    'Nombre' => $producto->nombre,
                    'Descripción' => $producto->descripcion ?? 'N/A',
                    'Categoría' => $producto->categoria->nombre ?? 'N/A',
                    'Laboratorio' => $producto->laboratorio->nombre ?? 'N/A',
                    'Stock' => $producto->lotes->sum('cantidad'), // Suma de todos los lotes
                    'Stock Mínimo' => $producto->stock_minimo,
                    'Stock Máximo' => $producto->stock_maximo,
                    'Precio Compra' => $lote->precio_compra,
                    'Precio Venta' => $lote->precio_venta,
                    'Fecha Ingreso' => $lote->fecha_ingreso->format('d/m/Y'),
                    'Fecha Vencimiento' => $producto->lotes->pluck('fecha_vencimiento')
                        ->filter()
                        ->min()?->format('d/m/Y') ?? 'N/A' // Fecha más próxima a vencer
                ];
            }

            // Para productos sin lotes
            return [
                'Código' => $producto->codigo,
                'Nombre' => $producto->nombre,
                'Descripción' => $producto->descripcion ?? 'N/A',
                'Categoría' => $producto->categoria->nombre ?? 'N/A',
                'Laboratorio' => $producto->laboratorio->nombre ?? 'N/A',
                'Stock' => 0,
                'Stock Mínimo' => $producto->stock_minimo,
                'Stock Máximo' => $producto->stock_maximo,
                'Precio Compra' => 'N/A',
                'Precio Venta' => 'N/A',
                'Fecha Ingreso' => 'N/A',
                'Fecha Vencimiento' => 'N/A'
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
                    'Código',
                    'Nombre',
                    'Descripción',
                    'Categoría',
                    'Laboratorio',
                    'Stock',
                    'Stock Mínimo',
                    'Stock Máximo',
                    'Precio Compra',
                    'Precio Venta',
                    'Fecha Ingreso',
                    'Fecha Vencimiento'
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
                    'A2:L' . $sheet->getHighestRow() => [
                        'alignment' => ['vertical' => 'center']
                    ],
                    // Centrar datos numéricos
                    'F2:L' . $sheet->getHighestRow() => [
                        'alignment' => ['horizontal' => 'center']
                    ]
                ];
            }
            },
            'reporte_productos_' . now()->format('YmdHis') . '.xlsx'
        );
    }

    private function generarCSV($productos): BinaryFileResponse
    {
        return $this->generarExcel($productos)
            ->setContentDisposition('attachment', 'reporte_productos_' . now()->format('YmdHis') . '.csv');
    }
}
