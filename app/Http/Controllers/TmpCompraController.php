<?php

namespace App\Http\Controllers;
use App\Models\Producto; 
use App\Models\TmpCompra;

use App\Models\Lote;
use Illuminate\Http\Request;

class TmpCompraController extends Controller
{
   
public function tmp_compras(Request $request){
        //buscar el producto envase al codigo que estamo
        $producto = Producto::where('codigo',$request->codigo)->first();
        //si el producto existe se registra en la tabla temporal 

$session_id = session()->getId();

       if($producto){

//si la compra existe que se pregunte en el productos id y tmb se pregunte en la session 
        $tmp_compra_existe = TmpCompra::where('producto_id',$producto->id)
                                        ->where('session_id',$session_id)
                                        ->first();
//si existe en ta compra en la base de datos  no se cree una nueva intansacion
if($tmp_compra_existe){
    $tmp_compra_existe->cantidad += $request->cantidad;
    $tmp_compra_existe->save();
    return response()->json(['success'=>true,'message'=>'el producto fue encontrado']);


}


        $tmp_compra =new TmpCompra();
        $tmp_compra->cantidad = $request->cantidad;
        $tmp_compra->producto_id = $producto->id;

        //diferencia un usuario logiado en otro equipo

         $tmp_compra->session_id = session()->getId();
         $tmp_compra->save();




            return response()->json(['success'=>true,'message'=>'el producto fue encontrado']);
       }else{
        return response()->json(['success'=>false,'message'=>'el producto no encontrado']);
       }

    }
    
    public function index()
    {
        //
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
    public function show(tmpCompra $tmpCompra)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(tmpCompra $tmpCompra)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, tmpCompra $tmpCompra)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
public function destroy($id)
{
    \DB::beginTransaction();
    try {
        // 1. Obtener el registro temporal
        $tmpCompra = TmpCompra::findOrFail($id);

        // 2. Eliminar el lote relacionado con este producto (si existe)
        $lote = Lote::where('producto_id', $tmpCompra->producto_id)
                    ->latest('id') // Puedes quitar esto si quieres eliminar todos
                    ->first();

        if ($lote) {
            $lote->delete();
        }

        // 3. Eliminar el registro temporal
        $tmpCompra->delete();

        \DB::commit();
        return response()->json(['success' => true]);
    } 
    catch (\Exception $e) {
    \Log::error('Error al eliminar producto y lote: ' . $e->getMessage());
    return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
}
}

}