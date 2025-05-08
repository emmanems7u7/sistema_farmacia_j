<?php

namespace App\Http\Controllers;

use App\Models\Correo;
use App\Models\PlantillaCorreo;
use App\Models\VariablesPlantillas;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\CorreoInterface;

class CorreoController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $correoRepository;
    public function __construct(CorreoInterface $CorreoInterface)
    {

        $this->correoRepository = $CorreoInterface;
    }
    public function index()
    {
        $emails = PlantillaCorreo::all();
        $variablesPlantilla = VariablesPlantillas::all();
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Correos', 'url' => route('correos.index')],
        ];

        return view('emails.index', compact('emails', 'breadcrumb', 'variablesPlantilla'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function GetPlantilla($id)
    {
        try {
            $email = PlantillaCorreo::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'email' => $email
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'mensaje' => 'Plantilla no encontrada.'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Correo $correo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Correo $correo)
    {
        //
    }
    public function update_plantilla(Request $request, $id)
    {

        try {
            $email = PlantillaCorreo::findOrFail($id);

            $request->validate([
                'nombre_plantilla' => 'required|string|max:255',
                'asunto_plantilla' => 'required|string|max:255',
                'contenido' => 'required|string',
            ]);

            $this->correoRepository->EditarPlantillaCorreo($request, $email);

            return redirect()->route('correos.index');

        } catch (ModelNotFoundException $e) {

            return redirect()->route('correos.index')
                ->with('error', 'La plantilla de correo no fue encontrada.');
        }


    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Correo $correo)
    {
        //
    }
}
