<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Ventas - {{ $mes }}</title>
    <style>
        /* Estilos generales */
        body { 
            font-family: 'Arial', sans-serif; 
            font-size: 10pt; 
            margin: 0; 
            padding: 0;
            color: #333;
        }
        .page-break { 
            page-break-after: always; 
        }
        
        /* Encabezado */
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            padding-bottom: 15px;
            background: linear-gradient(to right, #4CAF50, #2E7D32);
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .title { 
            font-size: 16pt; 
            font-weight: bold; 
            color: #0f0f0fff;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .subtitle { 
            font-size: 10pt; 
            color: #0f0f0fff;
            margin-bottom: 5px;
        }
        .info-reporte { 
            margin-top: 10px; 
            font-size: 9pt;
            color: #0f0f0fff;
        }
        
        /* Tablas */
        table { 
            width: 100%; 
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 25px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th { 
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            padding: 8px;
            text-align: left;
        }
        th, td { 
            border: 1px solid #e0e0e0; 
            padding: 8px; 
        }
        .text-right { 
            text-align: right; 
        }
        .text-center { 
            text-align: center; 
        }
        .text-left { 
            text-align: left; 
        }
        
        /* Estilos específicos para cada venta (colores alternados) */
        .venta-header { 
            background: linear-gradient(to right, #66BB6A, #43A047);
            color: white;
            font-weight: bold;
        }
        
        /* Colores alternados para cada venta */
        .venta-0 {
            border: 2px solid #66BB6A;
        }
        .venta-1 {
            border: 2px solid #5C6BC0;
        }
        .venta-2 {
            border: 2px solid #26A69A;
        }
        .venta-3 {
            border: 2px solid #FFA726;
        }
        .venta-4 {
            border: 2px solid #AB47BC;
        }
        
        /* Estilo para filas de detalles */
        .detalle-row:nth-child(even) { 
            background-color: #f8f9fa; 
        }
        .detalle-row:nth-child(odd) { 
            background-color: #ffffff; 
        }
        .detalle-row:hover {
            background-color: #f1f3f9;
        }
        
        /* Total de cada venta */
        .venta-total { 
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        
        /* Total general */
        .total-general { 
            background-color: #2d3436;
            color: white;
            font-weight: bold;
            font-size: 11pt;
        }
        
        /* Footer */
        .footer { 
            margin-top: 30px; 
            font-size: 8pt; 
            text-align: center; 
            color: #666; 
            border-top: 1px solid #ddd; 
            padding-top: 10px;
        }
        
        /* Estilo para cuando no hay datos */
        .no-data {
            background-color: #ffeaa7;
            text-align: center;
            padding: 15px;
            border-radius: 5px;
            color: #d63031;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Encabezado del reporte -->
    <div class="header">
    <div class="title">Reporte Detallado de Ventas</div>
    <div class="subtitle">{{ $mes }} | {{ $sucursalNombre }}</div>
    @if($sucursalDireccion)
        <div class="subtitle">{{ $sucursalDireccion }}</div>
    @endif
    <div class="info-reporte">
        Generado el: {{ $fechaGeneracion }} | 
        Total de ventas: {{ $ventas->count() }} | 
        Productos vendidos: {{ $totalProductosVendidos }}
    </div>
</div>

    @forelse($ventas as $index => $venta)
        <!-- Encabezado de cada venta -->
        <table class="venta-{{ $index % 5 }}">
            <tr class="venta-header">
                <td colspan="6">
                    <strong>Venta #{{ $venta->id }}</strong> | 
                    {{ $venta->created_at->format('d/m/Y H:i') }} |
                    Cliente: {{ $venta->cliente->nombre ?? 'Consumidor final' }} |
                    Vendedor: {{ $venta->usuario->name ?? 'N/A' }}
                </td>
            </tr>
            
            <!-- Encabezados -->
            <tr>
                <th class="text-left" width="35%">Producto</th>
                <th class="text-center" width="10%">Código</th>
                <th class="text-center" width="20%">Cantidad</th>
                <th class="text-right" width="20%">P. Venta</th>
                
                <th class="text-right" width="20%">Subtotal</th>
            </tr>
            
            <!-- Detalles de productos -->
            @foreach($venta->detallesVenta as $detalle)
            @php
                // Obtener el precio_venta del lote asociado al producto
                $precioVenta = $detalle->producto->lotes->first()->precio_venta ?? $detalle->precio_unitario;
            @endphp
            <tr class="detalle-row">
                <td>{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</td>
                <td class="text-center">{{ $detalle->producto->codigo ?? 'N/A' }}</td>
                <td class="text-center">{{ number_format($detalle->cantidad, 2) }}</td>
                <td class="text-right">${{ number_format($precioVenta, 2) }}</td>
              
                <td class="text-right">${{ number_format(($precioVenta * $detalle->cantidad) - ($detalle->descuento ?? 0), 2) }}</td>
            </tr>
            @endforeach
            
            <!-- Total de la venta -->
            <tr class="venta-total">
                <td colspan="4" class="text-right"><strong>TOTAL VENTA:</strong></td>
                <td class="text-right">
                    ${{ number_format($venta->precio_total, 2) }}
                </td>
            </tr>
        </table>
        
        <!-- Salto de página cada 5 ventas (excepto la última) -->
        @if(($index + 1) % 5 === 0 && !$loop->last)
            <div class="page-break"></div>
        @else
            <div style="margin-bottom: 20px;"></div>
        @endif
    @empty
        <table>
            <tr>
                <td class="no-data">No se encontraron ventas en este período</td>
            </tr>
        </table>
    @endforelse

    <!-- Total general -->
    @if($ventas->count() > 0)
    <table class="total-general-table">
        <tr class="total-general">
            <td class="text-right" width="80%"><strong>TOTAL GENERAL ({{ $ventas->count() }} ventas):</strong></td>
            <td class="text-right" width="20%">${{ number_format($totalVentas, 2) }}</td>
        </tr>
    </table>
    @endif

    <!-- Pie de página -->
    <div class="footer">
        Sistema de Ventas 
    </div>
</body>
</html>