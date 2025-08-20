<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Cajas</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; }
        .date-range { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; }
    </style>
</head>
<body>
    <style>
    .compact-header {
        text-align: center;
        padding: 12px 0;
        margin-bottom: 15px;
        border-bottom: 1px solid #e0e0e0;
        font-family: 'Segoe UI', Arial, sans-serif;
    }

    .compact-logo {
        margin: 0 auto 10px; /* Centrado con margen inferior reducido */
    }

    .compact-logo img {
        height: 65px; 
        width: auto;
        max-width: 150px;
    }

    .compact-title {
        margin: 0;
        font-size: 18px; 
        font-weight: 600;
        color: #2d3748;
        line-height: 1.3;
    }

    .compact-subtitle {
        margin: 4px 0 0;
        font-size: 12px;
        color: #4a5568;
    }

    .compact-meta {
        margin-top: 6px;
        font-size: 11px;
        color: #718096;
    }
</style>

<div class="compact-header">
    <div class="compact-logo">
        <img src="{{ public_path('assets/img/logofarmacia.jpeg') }}" alt="Logo Farmacia">
    </div>
    
    <div>
    <h1 class="compact-title">REPORTE DE CAJAS</h1>
<p class="compact-subtitle">Farmacia Mariel</p>
<div class="compact-meta">
    @isset($fecha_generacion)
        {{ $fecha_generacion }}
    @else
        {{ now()->format('d/m/Y H:i') }}
    @endisset
    | {{ Auth::user()->name ?? 'Sistema' }}
</div>
</div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha Apertura</th>
                <th>Fecha Cierre</th>
                <th>Monto Inicial</th>
                <th>Total Ingresos</th>
                <th>Total Egresos</th>
                <th>Saldo</th>
                <th>Sucursal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cajas as $caja)
            <tr>
                <td>{{ $caja->id }}</td>
                <td>{{ $caja->fecha_apertura }}</td>
                <td>{{ $caja->fecha_cierre ?? 'Abierta' }}</td>
                <td>{{ number_format($caja->monto_inicial, 2) }}</td>
                <td>{{ number_format($caja->total_ingresos, 2) }}</td>
                <td>{{ number_format($caja->total_egresos, 2) }}</td>
                <td>{{ number_format($caja->saldo, 2) }}</td>
                <td>{{ $caja->sucursal->nombre ?? 'N/A' }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4">TOTALES</td>
                <td>{{ number_format($totalGeneralIngresos, 2) }}</td>
                <td>{{ number_format($totalGeneralEgresos, 2) }}</td>
                <td>{{ number_format($saldoGeneral, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="footer">
        <p>Sistema de Gestión - {{ date('Y') }} </p>
        <p>Generado por: {{ Auth::user()->name ?? 'Sistema' }}</p>
    </div>
</body>
</html>