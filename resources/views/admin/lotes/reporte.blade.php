<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Lotes por Producto</title>
    <style>
        @page { 
            margin: 0.5cm 1.5cm; 
            size: A4 portrait; 
        }
        body { 
            margin: 0; 
            padding: 0; 
            font-family: Arial; 
            font-size: 9pt; 
            line-height: 1.2; 
        }
        .compact-header { 
            text-align: center; 
            padding: 5px 0; 
            margin-bottom: 10px;
            border-bottom: 1px solid #3498db;
        }
        .compact-logo img { 
            height: 60px; 
            width: auto; 
            max-width: 150px; 
        }
        .compact-title { 
            margin: 0; 
            font-size: 16px; 
            font-weight: bold;
            color: #2c3e50;
        }
        .product-group {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .product-header {
            background-color: #f8f9fa;
            padding: 5px;
            font-weight: bold;
            border-left: 4px solid #3498db;
            margin-bottom: 5px;
        }
        .product-name {
            font-size: 10pt;
            color: #2c3e50;
        }
        .product-code {
            font-size: 9pt;
            color: #7f8c8d;
            margin-left: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
            font-size: 8pt;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: white;
            font-size: 8.5pt;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .vencido { background-color: #ffdddd; }
        .proximo { background-color: #fff3cd; }
        .sin-fecha { background-color: #f5f5f5; }
        .footer { 
            margin-top: 15px;
            font-size: 8pt; 
            text-align: center;
            color: #7f8c8d;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .summary {
            margin-top: 10px;
            font-size: 9pt;
            text-align: right;
            padding: 5px;
            background-color: #f8f9fa;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="compact-header">
        <div class="compact-logo">
            <img src="{{ public_path('assets/img/logofarmacia.jpeg') }}" alt="Logo">
        </div>
        <h1 class="compact-title">REPORTE DETALLADO DE LOTES</h1>
        <div>Farmacia Mariel - {{ $fecha_generacion }}</div>
    </div>

    @php
        $groupedLotes = $lotes->groupBy('producto_id');
    @endphp

    @foreach($groupedLotes as $productId => $productLotes)
        @php
            $producto = $productLotes->first()->producto ?? null;
            $totalProducto = $productLotes->sum('cantidad');
        @endphp
        
        <div class="product-group">
            <div class="product-header">
                <span class="product-name">{{ $producto->nombre ?? 'Producto no encontrado' }}</span>
                <span class="product-code">(Código: {{ $producto->codigo ?? 'N/A' }})</span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Lote</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-right">P. Compra</th>
                        <th class="text-right">P. Venta</th>
                        <th class="text-center">Ingreso</th>
                        <th class="text-center">Vencimiento</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach($productLotes as $lote)
                    <tr class="@if($lote->estado == 'VENCIDO') vencido @elseif($lote->estado == 'SIN FECHA') sin-fecha @elseif(is_numeric($lote->dias_restantes) && $lote->dias_restantes <= 30) proximo @endif">
                        <td>{{ $lote->numero_lote }}</td>
                        <td class="text-right">{{ $lote->cantidad }}</td>
                        <td class="text-right">{{ number_format($lote->precio_compra, 2) }}</td>
                        <td class="text-right">{{ number_format($lote->precio_venta, 2) }}</td>
                        <td class="text-center">{{ $lote->fecha_ingreso_formatted }}</td>
                        <td class="text-center">{{ $lote->fecha_vencimiento_formatted }}</td>
                       
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>Total Producto</strong></td>
                        <td class="text-right"><strong>{{ $totalProducto }}</strong></td>
                        <td colspan="6"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endforeach

    <div class="summary">
        Total de productos: {{ $groupedLotes->count() }} | 
        Total de lotes: {{ $lotes->count() }} | 
        Stock total: {{ $lotes->sum('cantidad') }}
    </div>

    <div class="footer">
        Generado el {{ date('d/m/Y H:i') }} | Sistema de Gestión Farmacia Mariel - {{ date('Y') }}
    </div>
</body>
</html>