<?php
namespace App\Http\Controllers;
use App\Models\Categoria;

use App\Models\Catalogo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Interfaces\CatalogoInterface;

class CatalogoController extends Controller
{

    protected $CatalogoRepository;
    public function __construct(CatalogoInterface $CatalogoInterface)
    {

        $this->CatalogoRepository = $CatalogoInterface;
    }

    public function index(Request $request)
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Catalogo', 'url' => route('catalogos.index')],
        ];
        $categorias = Categoria::paginate(3, ['*'], 'categorias');

        $search = $request->get('search');


        $catalogos = Catalogo::with('categoria')
            ->when($search, function ($query, $search) {
                return $query->where('catalogo_codigo', 'like', "%{$search}%")
                    ->orWhere('catalogo_descripcion', 'like', "%{$search}%")
                    ->orWhereHas('categoria', function ($query) use ($search) {
                        $query->where('nombre', 'like', "%{$search}%");
                    });
            })
            ->paginate(10);
        return view('catalogo.index', compact('catalogos', 'categorias', 'breadcrumb'));
    }

    public function create()
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Catalogo', 'url' => route('catalogos.index')],
            ['name' => 'Crear Catalogo', 'url' => route('catalogos.index')],

        ];
        $categorias = Categoria::all();
        return view('catalogo.create', compact('breadcrumb', 'categorias'));

    }

    public function store(Request $request)
    {

        $request->validate([
            'categoria' => 'required|exists:categorias,id',
            'catalogo_parent' => 'nullable|string|max:5',
            'catalogo_codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('catalogos')->where(function ($query) use ($request) {
                    return $query->where('categoria_id', $request->categoria);
                }),
            ],
            'catalogo_descripcion' => 'required|string|max:100',
            'catalogo_estado' => 'required|integer|in:0,1',
        ]);

        $this->CatalogoRepository->GuardarCatalogo($request);

        return redirect()->route('catalogos.index')->with('success', 'Catálogo creado correctamente.');
    }


    public function edit($id)
    {
        $catalogo = Catalogo::findOrFail($id);
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Catalogo', 'url' => route('catalogos.index')],
            ['name' => 'Crear Catalogo', 'url' => route('catalogos.index')],

        ];

        $categorias = Categoria::all();

        return view('catalogo.edit', compact('id', 'catalogo', 'categorias', 'breadcrumb'));
    }

    public function update(Request $request, $id)
    {
        $catalogo = Catalogo::findOrFail($id);

        $request->validate([
            'categoria' => 'required|exists:categorias,id',
            'catalogo_parent' => 'nullable|string|max:5',
            'catalogo_codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('catalogos')->where(function ($query) use ($request) {
                    return $query->where('categoria_id', $request->categoria);
                })->ignore($catalogo->id),
            ],
            'catalogo_descripcion' => 'required|string|max:100',
            'catalogo_estado' => 'required|integer|in:0,1',
        ]);

        $this->CatalogoRepository->EditarCatalogo($request, $catalogo);


        return redirect()->route('catalogos.index')->with('success', 'Catálogo actualizado correctamente.');
    }

    public function destroy($id)
    {
        $catalogo = Catalogo::findOrFail($id);

        $catalogo->delete();

        return redirect()->back()->with('success', 'Catálogo eliminado correctamente.');
    }
}

