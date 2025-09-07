<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Bajo Stock</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h2, h4 {
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #17a2b8;
            color: #fff;
        }

        .text-center {
            text-align: center;
        }

        .danger {
            background-color: #f8d7da;
        }

        .warning {
            background-color: #fff3cd;
        }

        .info {
            background-color: #d1ecf1;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Reporte de Productos con Bajo Stock</h2>
        <h4>Stock mínimo filtrado: {{ $stockMinimo }}</h4>
        @if($sucursalId > 0)
            <p>Farmacia: {{ App\Models\Sucursal::find($sucursalId)->nombre ?? 'N/A' }}</p>
        @endif
        <p>Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th class="text-center">Stock Actual</th>
                <th class="text-center">Stock Mínimo</th>
                <th class="text-center">Nivel de Alerta</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $producto)
                @php
                    $stockActual = $producto->lotes_sum_cantidad;
                    $clase = '';
                    if ($stockActual <= 0) $clase = 'danger';
                    elseif ($stockActual <= 5) $clase = 'danger';
                    elseif ($stockActual <= 10) $clase = 'warning';
                    elseif ($stockActual <= 15) $clase = 'info';
                @endphp
                <tr class="{{ $clase }}">
                    <td>{{ $producto->codigo }}</td>
                    <td>{{ $producto->nombre }}</td>
                    <td class="text-center">{{ $stockActual }}</td>
                    <td class="text-center">{{ $producto->stock_minimo }}</td>
                    <td class="text-center">{{ $producto->nivel_alerta }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
