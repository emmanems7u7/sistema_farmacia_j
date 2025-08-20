<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Compras - {{ $mes->translatedFormat('F Y') }}</title>
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
            background: linear-gradient(to right, #4a6bdf, #2a4edb);
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .title { 
            font-size: 16pt; 
            font-weight: bold; 
            color: #0a0a0aff;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .subtitle { 
            font-size: 10pt; 
            color: #161515ff;
            margin-bottom: 5px;
        }
        .info-reporte { 
            margin-top: 10px; 
            font-size: 9pt;
            color: #0a0a0aff;
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
            background-color: #4a6bdf;
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
        
        /* Estilos específicos para cada compra (colores alternados) */
        .compra-header { 
            background: linear-gradient(to right, #6c5ce7, #5649d2);
            color: white;
            font-weight: bold;
        }
        
        /* Colores alternados para cada compra */
        .compra-0 {
            border: 2px solid #6c5ce7;
        }
        .compra-1 {
            border: 2px solid #00b894;
        }
        .compra-2 {
            border: 2px solid #e17055;
        }
        .compra-3 {
            border: 2px solid #0984e3;
        }
        .compra-4 {
            border: 2px solid #d63031;
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
        
        /* Total de cada compra */
        .compra-total { 
            background-color: #4a6bdf;
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
        <div class="title">Reporte Detallado de Compras</div>
        <div class="subtitle">{{ $mes->translatedFormat('F Y') }} | {{ $sucursalNombre }}</div>
        <div class="subtitle">{{ $sucursalDireccion }}</div>
        <div class="info-reporte">
            Generado el: {{ now()->format('d/m/Y H:i') }} | Total de compras: {{ $compras->count() }}
        </div>
    </div>

    @forelse($compras as $index => $compra)
        <!-- Encabezado de cada compra -->
<table class="compra-{{ $index % 5 }}">
    <tr class="compra-header">
        <td colspan="5">
            <strong>Compra #{{ $compra->id }}</strong> | 
            {{ Carbon\Carbon::parse($compra->fecha)->format('d/m/Y') }}
            
            Laboratorio: {{ $compra->laboratorio->nombre ?? 'N/A' }}
        </td>
    </tr>
    
    <!-- Encabezados -->
    <tr>
        <th class="text-left" width="35%">Producto</th>
        <th class="text-center" width="15%">Lote</th>
        <th class="text-center" width="15%">Cantidad</th>
        <th class="text-right" width="15%">P. Compra</th>
        <th class="text-right" width="20%">Subtotal</th>
    </tr>
    
    <!-- Detalles de productos -->
@foreach($compra->detalles as $detalle)
@php
    $lote = $detalle->lote ?? null;
    $precioCompra = $lote ? $lote->precio_compra : ($detalle->precio_compra ?? 0);
    $numeroLote = $lote ? $lote->numero_lote : 'N/A';
@endphp
<tr class="detalle-row">
    <td>{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</td>
    <td class="text-center">{{ $numeroLote }}</td>
    <td class="text-center">{{ number_format($detalle->cantidad, 2) }}</td>
    <td class="text-right">Bs{{ number_format($precioCompra, 2) }}</td>
    <td class="text-right">Bs{{ number_format($detalle->cantidad * $precioCompra, 2) }}</td>
</tr>
@endforeach
    
    <!-- Total de la compra -->
    <tr class="compra-total">
        <td colspan="4" class="text-right"><strong>TOTAL COMPRA:</strong></td>
        <td class="text-right">
            ${{ number_format($compra->detalles->sum(function($detalle) {
                return $detalle->cantidad * ($detalle->lote->precio_compra ?? $detalle->precio_compra ?? 0);
            }), 2) }}
        </td>
    </tr>
</table>



        
        <!-- Salto de página cada 5 compras (excepto la última) -->
        @if(($index + 1) % 5 === 0 && !$loop->last)
            <div class="page-break"></div>
        @else
            <div style="margin-bottom: 20px;"></div>
        @endif
    @empty
        <table>
            <tr>
                <td class="no-data">No se encontraron compras en este período</td>
            </tr>
        </table>
    @endforelse

    <!-- Total general -->
    @if($compras->count() > 0)
<table class="total-general-table">
    <tr class="total-general">
        <td class="text-right" width="80%"><strong>TOTAL GENERAL ({{ $compras->count() }} compras):</strong></td>
        <td class="text-right" width="20%">${{ number_format($compras->sum('precio_total'), 2) }}</td>
    </tr>
</table>
@endif

    <!-- Pie de página -->
    <div class="footer">
        Sistema de Inventarios 
    </div>
</body>
</html>