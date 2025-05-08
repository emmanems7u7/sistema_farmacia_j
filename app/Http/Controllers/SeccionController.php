<?php

namespace App\Http\Controllers;

use App\Models\Seccion;
use Illuminate\Http\Request;
use App\Interfaces\MenuInterface;
class SeccionController extends Controller
{
    protected $menuRepository;
    public function __construct(MenuInterface $MenuInterface)
    {

        $this->menuRepository = $MenuInterface;
    }
    // Mostrar todas las secciones
    public function index()
    {
        $secciones = Seccion::all();  // Obtener todas las secciones
        return view('secciones.index', compact('secciones'));
    }

    // Mostrar el formulario para crear una nueva sección
    public function create()
    {
        return view('secciones.create');
    }

    // Guardar una nueva sección
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255 |not_regex:/<\s*script/i',
            'icono' => 'required|string|max:255 |not_regex:/<\s*script/i',
        ]);

        $this->menuRepository->CrearSeccion($request);


        return redirect()->back()->with('success', 'Sección creada exitosamente.');
    }

    // Mostrar el formulario para editar una sección
    public function edit($id)
    {
        $seccion = Seccion::findOrFail($id);
        return view('secciones.edit', compact('seccion'));
    }

    // Actualizar una sección
    public function update(Request $request, $id)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
        ]);

        $seccion = Seccion::findOrFail($id);
        $seccion->update($request->all());  // Actualizar la sección
        return redirect()->route('secciones.index')->with('success', 'Sección actualizada exitosamente.');
    }

    // Eliminar una sección
    public function destroy($id)
    {
        $seccion = Seccion::findOrFail($id);
        $seccion->delete();  // Eliminar la sección
        return redirect()->back()->with('success', 'Sección eliminada exitosamente.');
    }
}
