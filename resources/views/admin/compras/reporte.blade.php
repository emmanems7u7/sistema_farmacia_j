<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Compras</title>
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
        <h1 class="compact-title">REPORTE DE COMPRAS</h1>
        <p class="compact-subtitle">Farmacia Mariel</p>
        <div class="compact-meta">
            {{ $fecha_generacion }} 
        </div>
    </div>
</div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Comprobante</th>
                <th>Laboratorio</th>
                <th class="text-right">Total</th>
                <th>Productos</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($compras as $compra)
            <tr>
                <td>{{ $compra->fecha }}</td>
                <td>{{ $compra->comprobante }}</td>
                <td>{{ $compra->laboratorio->nombre }}</td>
                <td class="text-right">{{ number_format($compra->precio_total, 2) }}</td>
                <td>{{ $compra->detalles->count() }}</td>
                <td>{{ $compra->detalles->sum('cantidad') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">Total General</th>
                <th class="text-right">{{ number_format($compras->sum('precio_total'), 2) }}</th>
                <th>{{ $compras->sum(function($c) { return $c->detalles->count(); }) }}</th>
                <th>{{ $compras->sum(function($c) { return $c->detalles->sum('cantidad'); }) }}</th>
            </tr>
        </tfoot>
    </table>

      <div class="footer">
        <p>Sistema de Gestión - {{ date('Y') }} </p>
       
    </div>
</body>
</html>