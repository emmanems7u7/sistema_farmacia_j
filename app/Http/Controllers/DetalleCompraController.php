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
        'codigo'   => 'required|string',
        'cantidad' => 'required|numeric|min:1',
        'id_compra'=> 'required|exists:compras,id',
        'lote_id'  => 'required|exists:lotes,id', // ahora es obligatorio
    ]);

    // Buscar el producto según el código
    $producto = Producto::where('codigo', $request->codigo)->first();

    if (!$producto) {
        return response()->json(['success' => false, 'message' => 'Producto no encontrado']);
    }

    $compra_id = $request->id_compra;

    // Buscar el lote seleccionado
    $lote = $producto->lotes()->find($request->lote_id);

    if (!$lote) {
        return response()->json(['success' => false, 'message' => 'Lote no encontrado']);
    }

    // Calcular la cantidad real (si compras en cajas, convertir a unidades)
    $unidades = $lote->cantidad_inicial > 0 ? $lote->cantidad_inicial : 1;
    $cantidadReal = $request->cantidad * $unidades;

    // Verificar si ya existe el detalle de compra
    $detalle_compra = DetalleCompra::where('producto_id', $producto->id)
        ->where('compra_id', $compra_id)
        ->where('lote_id', $lote->id)
        ->first();

    if ($detalle_compra) {
        // Actualizar cantidad y subtotal
        $detalle_compra->cantidad += $cantidadReal;
        $detalle_compra->subtotal = $detalle_compra->cantidad * $lote->precio_compra_unitario;
        $detalle_compra->save();

        // Actualizar cantidad del lote (sumar porque es compra)
        $lote->cantidad += $cantidadReal;
        $lote->save();

        return response()->json(['success' => true, 'message' => 'Cantidad del producto actualizada en el lote']);
    } else {
        // Crear nuevo detalle de compra
        $detalle_compra = new DetalleCompra();
        $detalle_compra->cantidad = $cantidadReal;
        $detalle_compra->compra_id = $compra_id;
        $detalle_compra->producto_id = $producto->id;
        $detalle_compra->lote_id = $lote->id;
        $detalle_compra->precio_unitario = $lote->precio_compra_unitario;
        $detalle_compra->subtotal = $cantidadReal * $lote->precio_compra_unitario;
        $detalle_compra->save();

        // Actualizar cantidad del lote (sumar stock)
        $lote->cantidad += $cantidadReal;
        $lote->save();

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
