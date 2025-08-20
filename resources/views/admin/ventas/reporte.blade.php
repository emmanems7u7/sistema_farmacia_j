<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Ventas</title>
    <style>
        @page { margin: 1cm; size: A4 landscape; }
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h1 { color: #2c3e50; font-size: 14pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #3498db; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .footer { margin-top: 20px; font-size: 9pt; text-align: right; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { font-weight: bold; background-color: #e6f7ff; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Ventas - {{ Auth::user()->sucursal->nombre ?? 'Sucursal' }}</h1>
        <p>Generado el: {{ $fecha_generacion }}</p>
        @if(request('fecha_inicio') && request('fecha_fin'))
        <p>Período: {{ request('fecha_inicio') }} al {{ request('fecha_fin') }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>N°</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th class="text-right">Total (Bs.)</th>
                <th class="text-center">Productos</th>
                <th class="text-center">Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
            <tr>
                <td>{{ $loop->iteration }}</td>
                
                <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                <td>{{ $venta->cliente->nombre_cliente ?? 'Cliente no especificado' }}</td>
                <td class="text-right">{{ number_format($venta->precio_total, 2) }}</td>
                <td class="text-center">{{ $venta->detallesVenta->count() }}</td>
                <td class="text-center">{{ $venta->detallesVenta->sum('cantidad') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right"><strong>TOTAL GENERAL</strong></td>
                <td class="text-right">{{ number_format($ventas->sum('precio_total'), 2) }}</td>
                <td class="text-center">{{ $ventas->sum(function($v) { return $v->detallesVenta->count(); }) }}</td>
                <td class="text-center">{{ $ventas->sum(function($v) { return $v->detallesVenta->sum('cantidad'); }) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Generado por: {{ Auth::user()->name }} | {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>