<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Caja #{{ $caja->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .logo {
            max-width: 150px;
            max-height: 80px;
        }
        .info-empresa {
            display: inline-block;
            vertical-align: top;
            margin-left: 20px;
        }
        .titulo-reporte {
            margin-top: 30px;
            font-size: 16px;
            font-weight: bold;
        }
        .datos-caja {
            margin: 15px 0;
            width: 100%;
        }
        .datos-caja td {
            padding: 5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .table th {
            background-color: #f2f2f2;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        .table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totales {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        .totales table {
            width: 100%;
        }
        .totales td {
            padding: 5px;
        }
        .totales .label {
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            border-top: 1px solid #333;
            padding-top: 10px;
            font-size: 10px;
            text-align: center;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($sucursal->logo)
            <img src="{{ storage_path('app/public/' . $sucursal->logo) }}" class="logo">
        @endif
        <div class="info-empresa">
            <h2>{{ $sucursal->nombre }}</h2>
            <p>{{ $sucursal->direccion }}</p>
            <p>Tel: {{ $sucursal->telefono }}</p>
        </div>
    </div>

    <div class="titulo-reporte">REPORTE DE CAJA #{{ $caja->id }}</div>

    <table class="datos-caja">
        <tr>
            <td width="20%"><strong>Fecha Apertura:</strong></td>
            <td width="30%">{{ $caja->fecha_apertura->format('d/m/Y H:i') }}</td>
            <td width="20%"><strong>Sucursal:</strong></td>
            <td width="30%">{{ $sucursal->nombre }}</td>
        </tr>
        <tr>
            <td><strong>Fecha Cierre:</strong></td>
            <td>{{ $caja->fecha_cierre ? $caja->fecha_cierre->format('d/m/Y H:i') : 'CAJA ABIERTA' }}</td>
            <td><strong>Monto Inicial:</strong></td>
            <td>{{ number_format($caja->monto_inicial, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Descripción:</strong></td>
            <td colspan="3">{{ $caja->descripcion ?? 'Sin descripción' }}</td>
        </tr>
    </table>

    <h3>Movimientos de Caja</h3>
    <table class="table">
        <thead>
            <tr>
                <th width="10%">Fecha</th>
                <th width="15%">Tipo</th>
                <th width="45%">Descripción</th>
                <th width="15%" class="text-right">Monto</th>
                <th width="15%" class="text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php
                $saldoAcumulado = $caja->monto_inicial;
            @endphp
            <tr>
                <td>{{ $caja->fecha_apertura->format('d/m/Y H:i') }}</td>
                <td>APERTURA</td>
                <td>Saldo inicial de caja</td>
                <td class="text-right">{{ number_format($caja->monto_inicial, 2) }}</td>
                <td class="text-right">{{ number_format($saldoAcumulado, 2) }}</td>
            </tr>
            
            @foreach($movimientos as $movimiento)
                @php
                    if($movimiento->tipo == 'INGRESO') {
                        $saldoAcumulado += $movimiento->monto;
                    } else {
                        $saldoAcumulado -= $movimiento->monto;
                    }
                @endphp
                <tr>
                    <td>{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $movimiento->tipo }}</td>
                    <td>{{ $movimiento->descripcion }}</td>
                    <td class="text-right">{{ number_format($movimiento->monto, 2) }}</td>
                    <td class="text-right">{{ number_format($saldoAcumulado, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totales">
        <table>
            <tr>
                <td class="label">Total Ingresos:</td>
                <td class="text-right">{{ number_format($totalIngresos, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Total Egresos:</td>
                <td class="text-right">{{ number_format($totalEgresos, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Saldo Final:</td>
                <td class="text-right">{{ number_format($saldoFinal, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Reporte generado el: {{ $fecha_generacion }}</p>
        <p>Sistema de Gestión {{ config('app.name') }}</p>
    </div>
</body>
</html>