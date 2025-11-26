<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $titulo ?? 'Reporte de Compras' }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #2c3e50; }
        .header p { margin: 5px 0; color: #7f8c8d; }
        .summary { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .summary-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; } 
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
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo ?? 'REPORTE DE COMPRAS' }}</h1>
        @if(isset($sucursal) && $sucursal)
            <p><strong>Sucursal:</strong> {{ $sucursal->nombre }}</p>
        @endif
        @if($tipo == 'rango')
            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fin)->format('d/m/Y') }}</p>
        @else
            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
        @endif
        <p><strong>Generado:</strong> {{ $fecha_generacion }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $totalCompras }}</div>
                <div class="summary-label">TOTAL COMPRAS</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">Bs {{ number_format($totalEgresos, 2) }}</div>
                <div class="summary-label">TOTAL GASTADO</div>
            </div>
        </div>
    </div>

    @if($compras->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Laboratorio</th>
                    <th>Proveedor</th>
                    <th class="text-right">Total Compra</th>
                </tr>
            </thead>
            <tbody>
                @foreach($compras as $index => $compra)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y') }}</td> 
                    <td>{{ $compra->laboratorio->nombre ?? 'S/D' }}</td>
                    <td>{{ $compra->laboratorio->nombre_proveedor ?? 'S/D' }}</td>
                    <td class="text-right">Bs {{ number_format($compra->precio_total, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="text-right"><strong>TOTAL:</strong></td>
                    <td class="text-right"><strong>Bs {{ number_format($totalEgresos, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #7f8c8d;">
            <h3>No hay compras registradas para este periodo</h3>
            @if($tipo == 'rango')
                <p>No se encontraron compras entre {{ \Carbon\Carbon::parse($inicio)->format('d/m/Y') }} y {{ \Carbon\Carbon::parse($fin)->format('d/m/Y') }}</p>
            @else
                <p>No se encontraron compras para {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
            @endif
        </div>
    @endif
</body>
</html>
