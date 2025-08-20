<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Models\Producto; 
use Illuminate\Http\Request;

class DetalleVentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
        // Buscar el producto según el código
        $producto = Producto::where('codigo', $request->codigo)->first();
        $venta_id = $request->id_venta;
    
        if ($producto) {
            // Verificar si hay suficiente stock
            if ($producto->stock < $request->cantidad) {
                return response()->json(['success' => false, 'message' => 'No hay suficiente stock']);
            }
    
            // Verificar si el producto ya está en la venta
            $detalle_venta_existe = DetalleVenta::where('producto_id', $producto->id)
                                                  ->where('venta_id', $venta_id)
                                                  ->first();
    
            // Si ya existe, actualizar la cantidad
            if ($detalle_venta_existe) {
                $detalle_venta_existe->cantidad += $request->cantidad;
                $detalle_venta_existe->save();
    
                $producto->stock -= $request->cantidad;
                $producto->save();
    
                return response()->json(['success' => true, 'message' => 'Cantidad del producto actualizada']);
            } else {
                // Si no existe, crear un nuevo detalle de venta
                $detalle_venta = new DetalleVenta();
                $detalle_venta->cantidad = $request->cantidad;
                $detalle_venta->venta_id = $venta_id;
                $detalle_venta->producto_id = $producto->id;
                $detalle_venta->save();
    
                $producto->stock -= $request->cantidad;
                $producto->save();
    
                return response()->json(['success' => true, 'message' => 'Producto agregado al detalle de venta']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado']);
        }
    }
    
    /**
     * Display the specified resource.
     */
    public function show(DetalleVenta $detalleVenta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DetalleVenta $detalleVenta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DetalleVenta $detalleVenta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $detalleVenta = DetalleVenta::find($id);
        $producto = Producto::find($detalleVenta->producto_id);
      
            $producto->stock += $detalleVenta->cantidad;
            $producto->save();
        
       
        DetalleVenta::destroy($id); // Buscar el usuario por ID
      

        return response()->json(['success'=>true]);
    }
}
