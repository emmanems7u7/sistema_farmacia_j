<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Sucursales</title>
    <style>
        @page {
            margin: 1cm;
            size: A4 portrait;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 10pt;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #2c3e50;
            font-size: 16pt;
            margin: 5px 0;
        }
        .header p {
            color: #7f8c8d;
            font-size: 10pt;
            margin: 3px 0;
        }
        .company-info {
            margin-bottom: 10px;
            text-align: center;
            font-size: 9pt;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 9pt;
        }
        thead tr {
            background-color: #3498db;
            color: #ffffff;
            text-align: left;
            font-weight: bold;
        }
        th, td {
            padding: 8px 10px;
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
            font-size: 9pt;
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
            max-width: 120px;
            max-height: 50px;
            margin-bottom: 10px;
        }
        .signature {
            margin-top: 30px;
            border-top: 1px dashed #ccc;
            width: 250px;
            padding-top: 10px;
            text-align: center;
            font-size: 9pt;
        }
        /* Anchuras específicas para columnas */
        .col-id { width: 8%; }
        .col-name { width: 20%; }
        .col-address { width: 25%; }
        .col-email { width: 15%; }
        .col-phone { width: 12%; }
        .col-date { width: 12%; }
        .col-status { width: 8%; }
    </style>
</head>
<body>
    <div class="header">
        <img src="https://ejemplo.com/logo.png" alt="Logo Empresa" class="logo">
        <h1>REPORTE DE SUCURSALES</h1>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-id">ID</th>
                <th class="col-name">Nombre</th>
                <th class="col-address">Dirección</th>
                <th class="col-email">Correo</th>
                <th class="col-phone">Teléfono</th>
                <th class="col-date">Fecha Registro</th>
               
            </tr>
        </thead>
        <tbody>
            @foreach($sucursals as $sucursal)
            <tr>
                <td class="col-id">{{ $sucursal->id }}</td>
                <td class="col-name">{{ $sucursal->nombre }}</td>
                <td class="col-address">{{ $sucursal->direccion }}</td>
                <td class="col-email">{{ $sucursal->email }}</td>
                <td class="col-phone">{{ $sucursal->telefono ?? 'N/A' }}</td>
                <td class="col-date">{{ $sucursal->created_at->format('d/m/Y') }}</td>
                
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado por: {{ Auth::user()->name ?? 'Sistema' }}</p>
        <p>Total sucursales: {{ count($sucursals) }}</p>
    </div>

    <div class="signature">
        <p>Responsable de sucursales</p>
        <p>_________________________</p>
    </div>
</body>
</html>