<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Factura de Venta - {{$sucursal->nombre}}</title>
    <style>
        /* Reset completo para impresión térmica */
        body, html {
            width: 80mm;
            margin: 0;
            padding: 0;
            font-family: 'Courier New', monospace;
            font-size: 9pt;
            line-height: 1.1;
        }
        body {
            padding: 2mm 3mm;
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .text-uppercase { text-transform: uppercase; }
        .text-small { font-size: 8pt; }
        
        /* Encabezado con logo */
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 2mm;
        }
        .logo {
            width: 25mm;
            height: auto;
            max-height: 25mm;
            margin-right: 2mm;
        }
        .header-text {
            flex: 1;
        }
        .company-name {
            font-size: 11pt;
            font-weight: bold;
            margin: 0 0 1mm 0;
            line-height: 1.2;
        }
        .company-info {
            font-size: 8pt;
            line-height: 1.2;
        }
        
        /* Título factura */
        .invoice-title {
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            margin: 1mm 0;
            padding: 1mm 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }
        
        /* Datos cliente */
        .client-info {
            margin-bottom: 2mm;
            line-height: 1.3;
        }
        
        /* Tabla de productos */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1mm 0;
        }
        
        th, td {
            padding: 1mm 0.5mm;
            vertical-align: top;
        }
        
        .item-qty { width: 12mm; text-align: center; }
        .item-desc { width: auto; }
        .item-price { width: 18mm; text-align: right; }
        .item-total { width: 18mm; text-align: right; }
        
        /* Totales */
        .total-row {
            font-weight: bold;
            border-top: 1px dashed #000;
        }
        
        /* Pie de factura */
        .payment-info {
            margin-top: 2mm;
            padding-top: 1mm;
            border-top: 1px dashed #000;
            line-height: 1.3;
        }
        
        .footer {
            margin-top: 3mm;
            font-size: 7pt;
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 1mm;
            line-height: 1.2;
        }
        
        /* Líneas divisorias */
        .divider {
            border-top: 1px dashed #000;
            margin: 2mm 0;
        }
    </style>
</head>
<body>

<!-- Encabezado con logo -->
<div class="header">
    @if(!empty($sucursal->imagen) && file_exists(public_path('storage/'.$sucursal->imagen)))
    <img src="{{ public_path('storage/'.$sucursal->imagen) }}" class="logo" alt="Logo">
    @endif
    <div class="header-text">
        
        <div class="company-info">
            
            Tel: {{$sucursal->telefono}}<br>
            {{$sucursal->direccion}}
        </div>
    </div>
</div>

<!-- Título del documento -->
<div class="invoice-title text-uppercase">factura de venta</div>

<?php
$fecha_db = $venta->fecha;
$fecha_formateada = date("d", strtotime($fecha_db)) ." de ".
date("F", strtotime($fecha_db)) . " de " .
date("Y", strtotime($fecha_db));
$meses = [
    'January' => 'enero',
    'February' => 'febrero',
    'March' => 'marzo',
    'April' => 'abril',
    'May' => 'mayo',
    'June' => 'junio',
    'July' => 'julio',
    'August' => 'agosto',
    'September' => 'septiembre',
    'October' => 'octubre',
    'November' => 'noviembre',
    'December' => 'diciembre'
];
$fecha_formateada = str_replace(array_keys($meses), array_values($meses), $fecha_formateada);
?>

<!-- Datos del cliente (con validación) -->
<div class="client-info">
    @if(isset($venta->cliente))
    <div class="text-bold">Cliente: {{$venta->cliente->nombre_cliente ?? 'SIN NOMBRE'}}</div>
    <div>NIT/CI: {{$venta->cliente->nit_ci ?? '0'}}</div>
    @else
    <div class="text-bold">Cliente: SIN NOMBRE</div>
    <div>NIT/CI: 0</div>
    @endif
    <div>Fecha: {{$fecha_formateada}}</div>
</div>

<div class="divider"></div>

<!-- Detalle de la venta -->
<!-- Detalle de la venta -->
<!-- Detalle de la venta -->
<table>
    <thead>
        <tr class="text-bold">
            <th class="item-qty">Cant.</th>
            <th class="item-desc">Descripción</th>
            <th class="item-price">P.Unit</th>
            <th class="item-total">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @php
        $contador = 1;
        $subtotal = 0;
        $suma_cantidad = 0;
        $suma_subtotal = 0;
        @endphp

        @foreach($venta->detallesVenta as $detalle)
        @php
        // Obtenemos el lote más reciente para este producto
        $lote = $detalle->producto->lotes()->orderBy('created_at', 'desc')->first();
        
        // Usamos el precio del lote si existe, sino del producto
        $precio_unitario = $lote ? $lote->precio_venta : $detalle->producto->precio_venta;
        $subtotal = $detalle->cantidad * $precio_unitario;
        $suma_subtotal += $subtotal;
        $suma_cantidad += $detalle->cantidad;
        @endphp
        <tr>
            <td class="item-qty">{{$detalle->cantidad}}</td>
            <td class="item-desc">{{$detalle->producto->nombre}}</td>
            <td class="item-price">Bs {{number_format($precio_unitario, 2, '.', ',')}}</td>
            <td class="item-total">Bs {{number_format($subtotal, 2, '.', ',')}}</td>
        </tr>
        @endforeach

        <!-- Totales -->
        <tr class="total-row">
            <td class="item-qty">{{$suma_cantidad}}</td>
            <td class="item-desc" colspan="2">TOTAL</td>
            <td class="item-total">Bs {{number_format($suma_subtotal, 2, '.', ',')}}</td>
        </tr>
    </tbody>
</table>

<!-- Información de pago -->
<div class="payment-info">
    <div class="text-bold">Total a pagar: Bs {{number_format($suma_subtotal, 2, '.', ',')}}</div>
    

<div>Son: {{ $literal }}</div>

</div>



<!-- Pie de página -->
<div class="footer">
    {{date('d/m/Y H:i:s')}}<br>
    ¡Gracias por su compra!<br>
   
</div>

</body>
</html>