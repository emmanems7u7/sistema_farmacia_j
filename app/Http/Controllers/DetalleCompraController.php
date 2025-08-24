<?php

namespace App\Http\Controllers;

use App\Models\DetalleCompra;
use App\Models\Producto;
use Illuminate\Http\Request;

class DetalleCompraController extends Controller
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
        // Validar entrada
        $request->validate([
            'codigo' => 'required|string',
            'cantidad' => 'required|numeric|min:1',
            'id_compra' => 'required|exists:compras,id',
        ]);

        // Buscar el producto según el código
        $producto = Producto::where('codigo', $request->codigo)->first();

        if (!$producto) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado']);
        }

        $compra_id = $request->id_compra;

        // Validar stock disponible
        if ($request->cantidad > $producto->stock) {
            return response()->json(['success' => false, 'message' => 'Stock insuficiente']);
        }

        // Verificar si el producto ya está en la compra
        $detalle_compra = DetalleCompra::where('producto_id', $producto->id)
            ->where('compra_id', $compra_id)
            ->first();

        if ($detalle_compra) {
            // Actualizar cantidad
            $detalle_compra->cantidad += $request->cantidad;
            $detalle_compra->save();

            $producto->stock -= $request->cantidad;
            $producto->save();

            return response()->json(['success' => true, 'message' => 'Cantidad del producto actualizada']);
        } else {
            // Crear nuevo detalle de compra
            $detalle_compra = new DetalleCompra();
            $detalle_compra->cantidad = $request->cantidad;
            $detalle_compra->compra_id = $compra_id;
            $detalle_compra->producto_id = $producto->id;
            $detalle_compra->save();

            $producto->stock -= $request->cantidad;
            $producto->save();

            return response()->json(['success' => true, 'message' => 'Producto agregado al detalle de compra']);
        }
    }
    public function show(detalleCompra $detalleCompra)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(detalleCompra $detalleCompra)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, detalleCompra $detalleCompra)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $detalleCompra = DetalleCompra::find($id);
        $producto = Producto::find($detalleCompra->producto_id);

        $producto->stock += $detalleCompra->cantidad;
        $producto->save();


        DetalleCompra::destroy($id); // Buscar el usuario por ID


        return response()->json(['success' => true]);
    }
}
