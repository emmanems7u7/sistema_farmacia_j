<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Compra {{ $compra->comprobante }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; color: #333; }
        .header p { margin: 3px 0; }
        .table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        .table th { background-color: #f8f9fa; text-align: left; padding: 5px; border: 1px solid #ddd; }
        .table td { padding: 5px; border: 1px solid #ddd; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-box { margin-top: 10px; float: right; width: 50%; }
        .footer { margin-top: 30px; font-size: 10px; text-align: center; color: #666; }
        .signature { margin-top: 50px; border-top: 1px dashed #333; width: 200px; }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <h2>Farmacia {{ $sucursal->nombre }}</h2>
        <p>{{ $sucursal->direccion }}</p>
        <p>Teléfono: {{ $sucursal->telefono }}</p>
        <p><strong>COMPROBANTE DE COMPRA</strong></p>
        <p>N°: {{ $compra->comprobante }} | Fecha: {{ date('d/m/Y', strtotime($compra->fecha)) }}</p>
    </div>

    <!-- Datos del Laboratorio/Proveedor -->
 <!-- Tabla de Información del Proveedor Mejorada -->
<table class="table" style="width: 100%; border-collapse: collapse; margin: 3px 0; font-family: 'Courier New', monospace; font-size: 10px;">
    <tr>
        <th colspan="2" style="padding: 3px; border: 1px solid #000; text-align: left; font-weight: bold;">
            PROVEEDOR
        </th>
    </tr>
    <tr>
        <td width="30%" style="padding: 3px; border: 1px solid #000; font-weight: bold;">Nombre:</td>
        <td style="padding: 3px; border: 1px solid #000;">
            {{ strlen($compra->laboratorio->nombre ?? '') > 25 ? substr($compra->laboratorio->nombre, 0, 22).'...' : ($compra->laboratorio->nombre ?? 'N/A') }}
        </td>
    </tr>
    <tr>
        <td style="padding: 3px; border: 1px solid #000; font-weight: bold;">NIT/CI:</td>
        <td style="padding: 3px; border: 1px solid #000;">{{ $compra->laboratorio->nit ?? 'N/A' }}</td>
    </tr>
</table>

<!-- Tabla de Detalle de Productos Mejorada -->
<table class="table" style="width: 100%; border-collapse: collapse; margin: 5px 0; font-family: 'Courier New', monospace; font-size: 10px;">
    <thead>
        <tr>
            <th style="width: 5%; padding: 3px; border: 1px solid #000; text-align: center;">#</th>
            <th style="width: 45%; padding: 3px; border: 1px solid #000; text-align: left;">PRODUCTO</th>
            <th style="width: 15%; padding: 3px; border: 1px solid #000; text-align: center;">CANT</th>
            <th style="width: 17%; padding: 3px; border: 1px solid #000; text-align: right;">P.UNIT</th>
            <th style="width: 18%; padding: 3px; border: 1px solid #000; text-align: right;">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($compra->detalles as $detalle)
        @php
            $lote = $detalle->producto->lotes->first();
            $precio_compra = $lote ? $lote->precio_compra : 0;
            $subtotal = $detalle->cantidad * $precio_compra;
            $nombreProducto = strlen($detalle->producto->nombre ?? '') > 25 ? substr($detalle->producto->nombre, 0, 22).'...' : ($detalle->producto->nombre ?? 'Prod.');
        @endphp
        <tr>
            <td style="padding: 3px; border: 1px solid #000; text-align: center;">{{ $loop->iteration }}</td>
            <td style="padding: 3px; border: 1px solid #000;">{{ $nombreProducto }}</td>
            <td style="padding: 3px; border: 1px solid #000; text-align: center;">{{ $detalle->cantidad }}</td>
            <td style="padding: 3px; border: 1px solid #000; text-align: right;">Bs{{ number_format($precio_compra, 2) }}</td>
            <td style="padding: 3px; border: 1px solid #000; text-align: right;">Bs{{ number_format($subtotal, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Tabla de Totales Mejorada -->
<div style="width: 100%; margin-top: 20px;">
    <table style="width: 50%; margin-left: auto; border-collapse: collapse; font-family: Arial, sans-serif;">
        
        
        <tr>
            <th style="padding: 8px; text-align: left; font-weight: bold; border-bottom: 2px solid #333;">TOTAL:</th>
            <td style="padding: 8px; text-align: right; font-weight: bold; border-bottom: 2px solid #333;">Bs {{ number_format($compra->precio_total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 10px 8px 0 8px; font-size: 12px; color: #555;">
                <strong>Total en letras:</strong> {{ $literal }}
            </td>
        </tr>
    </table>
</div>

    <!-- Firmas y footer -->
    <div style="clear: both;"></div>
    <div class="signature">
        <p class="text-center">Firma del Responsable</p>
    </div>
    
    <div class="footer">
        Generado el: {{ $fecha_generacion }} 
    </div>
</body>
</html>