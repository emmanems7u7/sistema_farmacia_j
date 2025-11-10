<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas del Día</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #2c3e50; }
        .header p { margin: 5px 0; color: #7f8c8d; }
        .summary { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .summary-item { text-align: center; padding: 10px; }
        .summary-value { font-size: 24px; font-weight: bold; color: #2c3e50; }
        .summary-label { font-size: 14px; color: #7f8c8d; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #34495e; color: white; padding: 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background-color: #2c3e50 !important; color: white; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; color: #7f8c8d; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE VENTAS DEL DÍA</h1>
        <p><strong>Sucursal:</strong> {{ $sucursal->nombre }}</p>
        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
        <p><strong>Generado:</strong> {{ $fecha_generacion }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $totalVentas }}</div>
                <div class="summary-label">TOTAL VENTAS</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">Bs {{ number_format($totalIngresos, 2) }}</div>
                <div class="summary-label">INGRESOS TOTALES</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $totalProductos }}</div>
                <div class="summary-label">PRODUCTOS VENDIDOS</div>
            </div>
        </div>
    </div>

    @if($ventas->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha y Hora</th>
                    <th>Cliente</th>
                    <th>NIT/CI</th>
                    <th>Productos</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventas as $index => $venta)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($venta->created_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $venta->cliente->nombre_cliente ?? 'S/N' }}</td>
                    <td>{{ $venta->cliente->nit_ci ?? 'N/A' }}</td>
                    <td>{{ $venta->detallesVenta->count() }} productos</td>
                    <td class="text-right">Bs {{ number_format($venta->precio_total, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5" class="text-right"><strong>TOTAL DEL DÍA:</strong></td>
                    <td class="text-right"><strong>Bs {{ number_format($totalIngresos, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Detalle por productos más vendidos -->
        <h3 style="margin-top: 30px;">Productos Más Vendidos</h3>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-center">Cantidad Vendida</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $productosVendidos = [];
                    foreach($ventas as $venta) {
                        foreach($venta->detallesVenta as $detalle) {
                            $productoNombre = $detalle->producto->nombre;
                            if(!isset($productosVendidos[$productoNombre])) {
                                $productosVendidos[$productoNombre] = [
                                    'cantidad' => 0,
                                    'total' => 0
                                ];
                            }
                            $precio = $detalle->lote->precio_venta ?? $detalle->producto->precio_venta;
                            $productosVendidos[$productoNombre]['cantidad'] += $detalle->cantidad;
                            $productosVendidos[$productoNombre]['total'] += $detalle->cantidad * $precio;
                        }
                    }
                    arsort($productosVendidos);
                @endphp

                @foreach(array_slice($productosVendidos, 0, 10) as $producto => $datos)
                <tr>
                    <td>{{ $producto }}</td>
                    <td class="text-center">{{ $datos['cantidad'] }}</td>
                    <td class="text-right">Bs {{ number_format($datos['total'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #7f8c8d;">
            <h3>No hay ventas registradas para esta fecha</h3>
            <p>No se encontraron ventas para el día {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
        </div>
    @endif

    
</body>
</html>