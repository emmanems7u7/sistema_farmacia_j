<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }}</title>
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
        .total-row { background-color: #2c3e50 !important; color: white; font-weight: bold; }
    </style>
</head>
<body>

<div class="header">
    <h1>{{ strtoupper($titulo) }}</h1>
    <p><strong>Sucursal:</strong> {{ $sucursal->nombre }}</p>

    @if($inicio === $fin)
        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($inicio)->format('d/m/Y') }}</p>
    @else
        <p>
            <strong>Desde:</strong> {{ \Carbon\Carbon::parse($inicio)->format('d/m/Y') }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>Hasta:</strong> {{ \Carbon\Carbon::parse($fin)->format('d/m/Y') }}
        </p>
    @endif

    <p><strong>Generado:</strong> {{ $fecha_generacion }}</p>
</div>


<div class="summary">
    <div class="summary-grid">
        <div class="summary-item">
            <div class="summary-value">{{ $totalVentas }}</div>
            <div class="summary-label">VENTAS</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">Bs {{ number_format($totalIngresos, 2) }}</div>
            <div class="summary-label">INGRESOS</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $totalProductos }}</div>
            <div class="summary-label">PRODUCTOS</div>
        </div>
    </div>
</div>

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

        @php
            $totalTablaVentas = 0;
        @endphp

        @foreach($ventas as $index => $venta)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($venta->created_at)->format('d/m/Y H:i') }}</td>
            <td>{{ $venta->cliente->nombre_cliente ?? 'S/N' }}</td>
            <td>{{ $venta->cliente->nit_ci ?? 'N/A' }}</td>
            <td>{{ $venta->detallesVenta->count() }} productos</td>
            <td class="text-right">Bs {{ number_format($venta->precio_total, 2) }}</td>
        </tr>

        @php
            $totalTablaVentas += $venta->precio_total;
        @endphp

        @endforeach

        <!-- Fila TOTAL -->
        <tr class="total-row">
            <td colspan="5" style="text-align: right; font-weight: bold;">TOTAL</td>
            <td class="text-right" style="font-weight: bold;">
                Bs {{ number_format($totalTablaVentas, 2) }}
            </td>
        </tr>

    </tbody>
</table>



<!-- ================================ -->
<!-- SECCIÓN: PRODUCTOS MÁS VENDIDOS -->
<!-- ================================ -->

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

            // Ordenar por total de ventas en desc
            uasort($productosVendidos, function($a, $b) {
                return $b['total'] <=> $a['total'];
            });

            // Calcular totales generales
            $totalCantidad = array_sum(array_column($productosVendidos, 'cantidad'));
            $totalBs = array_sum(array_column($productosVendidos, 'total'));
        @endphp

        @foreach(array_slice($productosVendidos, 0, 10) as $producto => $datos)
        <tr>
            <td>{{ $producto }}</td>
            <td class="text-center">{{ $datos['cantidad'] }}</td>
            <td class="text-right">Bs {{ number_format($datos['total'], 2) }}</td>
        </tr>
        @endforeach

        <!-- Fila de total -->
        <tr class="total-row">
            <td style="font-weight: bold;">TOTAL</td>
            <td class="text-center" style="font-weight: bold;">{{ $totalCantidad }}</td>
            <td class="text-right" style="font-weight: bold;">Bs {{ number_format($totalBs, 2) }}</td>
        </tr>

    </tbody>
</table>


</body>
</html>
