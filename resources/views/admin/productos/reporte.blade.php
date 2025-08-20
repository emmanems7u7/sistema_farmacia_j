<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Productos con Lotes</title>
    <style>
        @page {
            margin: 1cm;
            size: A4 landscape;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 9pt;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #2c3e50;
            font-size: 14pt;
            margin: 5px 0;
        }
        .header p {
            color: #7f8c8d;
            font-size: 9pt;
            margin: 3px 0;
        }
        .company-info {
            margin-bottom: 10px;
            text-align: center;
            font-size: 8pt;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 8pt;
            table-layout: fixed;
        }
        thead tr {
            background-color: #3498db;
            color: #ffffff;
            text-align: left;
            font-weight: bold;
        }
        th, td {
            padding: 6px 8px;
            border: 1px solid #e0e0e0;
            word-wrap: break-word;
        }
        tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }
        tbody tr:nth-of-type(even) {
            background-color: #f9f9f9;
        }
        tbody tr:last-of-type {
            border-bottom: 2px solid #3498db;
        }
        tbody tr:hover {
            background-color: #f1f9ff;
        }
        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 8pt;
            color: #7f8c8d;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .highlight {
            background-color: #fffde7;
        }
        .logo {
            max-width: 100px;
            max-height: 40px;
            margin-bottom: 5px;
        }
        .signature {
            margin-top: 20px;
            border-top: 1px dashed #ccc;
            width: 200px;
            padding-top: 5px;
            text-align: center;
            font-size: 8pt;
        }
        /* Anchuras específicas para columnas */
        .col-code { width: 6%; }
        .col-name { width: 12%; }
        .col-desc { width: 14%; }
        .col-cat { width: 8%; }
        .col-lab { width: 8%; }
        .col-stock { width: 5%; }
        .col-min { width: 5%; }
        .col-max { width: 5%; }
        .col-lote { width: 6%; }
        .col-price-buy { width: 7%; }
        .col-price-sell { width: 7%; }
        .col-date-in { width: 7%; }
        .col-date-out { width: 7%; }
        .col-expired {
            width: 5%;
            background-color: #ffebee;
        }
    </style>
</head>
<body>
    <div class="header">
       
        <h1>REPORTE DE INVENTARIO DE PRODUCTOS Y LOTES</h1>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="company-info">
        <p class="compact-subtitle">Farmacia Mariel</p>| 
        Dirección: Zon Vino tinto
        Teléfono: 75260345 
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-code">Código</th>
                <th class="col-name">Nombre</th>
                <th class="col-desc">Descripción</th>
                <th class="col-cat">Categoría</th>
                <th class="col-lab">Laboratorio</th>
                <th class="col-stock text-right">Stock</th>
                <th class="col-min text-right">Mín</th>
                <th class="col-max text-right">Máx</th>
                <th class="col-lote">Lote</th>
                <th class="col-price-buy text-right">P. Compra</th>
                <th class="col-price-sell text-right">P. Venta</th>
                <th class="col-date-in text-center">Ingreso</th>
                <th class="col-date-out text-center">Vencim.</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $producto)
                @php
                    $loteActivo = $producto->lotes()->orderBy('fecha_vencimiento', 'asc')->first();
                    $stockTotal = $producto->lotes()->sum('cantidad');
                @endphp
                
                <tr class="{{ $stockTotal < $producto->stock_minimo ? 'highlight' : '' }}">
                    <td class="col-code">{{ $producto->codigo }}</td>
                    <td class="col-name">{{ Str::limit($producto->nombre, 20) }}</td>
                    <td class="col-desc">{{ Str::limit($producto->descripcion, 30) }}</td>
                    <td class="col-cat">{{ Str::limit($producto->categoria->nombre, 15) }}</td>
                    <td class="col-lab">{{ Str::limit($producto->laboratorio->nombre, 15) }}</td>
                    <td class="col-stock text-right">{{ $stockTotal }}</td>
                    <td class="col-min text-right">{{ $producto->stock_minimo }}</td>
                    <td class="col-max text-right">{{ $producto->stock_maximo }}</td>
                    <td class="col-lote">{{ $loteActivo ? $loteActivo->numero_lote : 'N/A' }}</td>
                    <td class="col-price-buy text-right">
                        {{ $loteActivo ? number_format($loteActivo->precio_compra, 2) : 'N/A' }}
                    </td>
                    <td class="col-price-sell text-right">
                        {{ $loteActivo ? number_format($loteActivo->precio_venta, 2) : 'N/A' }}
                    </td>
                    <td class="col-date-in text-center">
                        @if($loteActivo && $loteActivo->fecha_ingreso)
                            {{ \Carbon\Carbon::parse($loteActivo->fecha_ingreso)->format('d/m/Y') }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="col-date-out text-center">
                        @if($loteActivo && $loteActivo->fecha_vencimiento)
                            {{ \Carbon\Carbon::parse($loteActivo->fecha_vencimiento)->format('d/m/Y') }}
                        @else
                            N/A
                        @endif
                    </td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>

     <div class="footer">
        <p>Sistema de Gestión - {{ date('Y') }} </p>
      
    </div>

    <div class="signature">
        <p>Responsable del inventario</p>
        <p>_________________________</p>
    </div>
</body>
</html>