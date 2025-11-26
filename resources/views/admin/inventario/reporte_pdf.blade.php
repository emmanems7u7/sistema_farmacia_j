<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        /* Estilos generales */
        body { 
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif; 
            font-size: 12px; 
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 15px;
        }
        
        /* Encabezado */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
        }
        
        .header h2 {
            margin: 0;
            color: #2c3e50;
            font-size: 22px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header .subtitle {
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 5px;
        }
        
        /* Información del reporte */
        .report-info {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #3498db;
        }
        
        .report-info p {
            margin: 5px 0;
        }
        
        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        th {
            background: #34495e;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #2c3e50;
        }
        
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Estados de vencimiento */
        .vencido {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .proximo {
            color: #e67e22;
            font-weight: bold;
        }
        
        .vigente {
            color: #27ae60;
        }
        
        /* Pie de página */
        .footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #7f8c8d;
            font-size: 10px;
        }
        
        /* Resumen */
        .summary {
            margin-top: 15px;
            padding: 10px;
            background: #ecf0f1;
            border-radius: 5px;
            font-size: 11px;
        }
        
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>{{ $titulo }}</h2>
        <div class="subtitle">Reporte generado el {{ date('d/m/Y H:i') }}</div>
    </div>

    <div class="report-info">
        <p><strong>Fecha de corte:</strong> {{ date('d/m/Y') }}</p>
        <p><strong>Total de registros:</strong> {{ $lotes->count() }}</p>
        @if($tipo == 'vencidos')
        <p><strong>Estado:</strong> <span class="vencido">PRODUCTOS VENCIDOS</span></p>
        @elseif($tipo == '15')
        <p><strong>Estado:</strong> <span class="proximo">VENCEN EN PRÓXIMOS 15 DÍAS</span></p>
        @elseif($tipo == '30')
        <p><strong>Estado:</strong> <span class="proximo">VENCEN EN PRÓXIMOS 30 DÍAS</span></p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="35%">Producto</th>
                <th width="20%">Código Lote</th>
                <th width="15%">Cantidad</th>
                <th width="20%">Vencimiento</th>
                <th width="10%">Días Restantes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lotes as $lote)
            @php
                // Calcular días restantes para vencimiento de forma precisa
                $fechaVencimiento = \Carbon\Carbon::parse($lote->fecha_vencimiento);
                $hoy = \Carbon\Carbon::now()->startOfDay();
                $diasRestantes = $hoy->diffInDays($fechaVencimiento, false);
                
                // Determinar clase CSS según estado
                $claseEstado = '';
                if ($diasRestantes < 0) {
                    $claseEstado = 'vencido';
                } elseif ($diasRestantes <= 15) {
                    $claseEstado = 'proximo';
                } else {
                    $claseEstado = 'vigente';
                }
            @endphp
            <tr>
                <td>{{ $lote->producto->nombre ?? 'Producto no disponible' }}</td>
                <td>{{ $lote->numero_lote ?? 'N/A' }}</td>
                <td class="text-center">{{ $lote->cantidad }}</td>
                <td class="{{ $claseEstado }}">{{ $fechaVencimiento->format('d/m/Y') }}</td>
                <td class="text-center {{ $claseEstado }}">
                    @if($diasRestantes < 0)
                        <strong>Vencido</strong>
                    @else
                        {{ $diasRestantes }} días
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($lotes->count() > 0)
    <div class="summary">
        <p><strong>Resumen:</strong> 
            Se encontraron <strong>{{ $lotes->count() }}</strong> 
            @if($tipo == 'vencidos')
                productos vencidos.
            @elseif($tipo == '15')
                productos que vencen en los próximos 15 días.
            @elseif($tipo == '30')
                productos que vencen en los próximos 30 días.
            @endif
        </p>
    </div>
    @else
    <div style="text-align: center; margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px;">
        <p>No se encontraron registros para el criterio seleccionado.</p>
    </div>
    @endif

   

</body>
</html>