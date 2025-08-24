<!DOCTYPE html>
<html>

<head>
    <title>Reporte de Usuarios</title>
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

        th,
        td {
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
        .col-id {
            width: 5%;
        }

        .col-name {
            width: 10%;
        }

        .col-email {
            width: 12%;
        }

        .col-username {
            width: 8%;
        }

        .col-role {
            width: 8%;
        }

        .col-sucursal {
            width: 10%;
        }

        .col-address {
            width: 15%;
        }

        .col-phone {
            width: 8%;
        }

        .col-status {
            width: 6%;
        }

        .col-created {
            width: 8%;
        }

        .col-updated {
            width: 8%;
        }
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
            margin: 0 auto 10px;
            /* Centrado con margen inferior reducido */
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
            <h1 class="compact-title">REPORTE DE USUARIOS</h1>
            <p class="compact-subtitle">Farmacia Mariel</p>
            <div class="compact-meta">
                {{ $fecha_generacion }} | {{ Auth::user()->name ?? ''  }}
            </div>
        </div>
    </div>



    <table>
        <thead>
            <tr>
                <th class="col-id">ID</th>
                <th class="col-name">Nombres</th>
                <th class="col-name">Apellidos</th>
                <th class="col-email">Correo</th>
                <th class="col-username">Usuario</th>

                <th class="col-sucursal">Sucursal</th>
                <th class="col-address">Dirección</th>
                <th class="col-phone">Teléfono</th>



            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
                <tr>
                    <td class="col-id">{{ $usuario->id }}</td>
                    <td class="col-name">{{ $usuario->usuario_nombres }}</td>
                    <td class="col-name">{{ $usuario->usuario_app }} {{ $usuario->usuario_apm }}</td>
                    <td class="col-email">{{ $usuario->email }}</td>
                    <td class="col-username">{{ $usuario->name }}</td>

                    <td class="col-sucursal">{{ $usuario->sucursal->nombre ?? 'N/A' }}</td>
                    <td class="col-address">{{ Str::limit($usuario->address, 25) }}</td>
                    <td class="col-phone">{{ $usuario->celular ?? 'N/A' }}</td>



                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema de Gestión - {{ date('Y') }} </p>

    </div>

    <div class="signature">
        <p>Responsable de usuarios</p>
        <p>_________________________</p>
    </div>
</body>

</html>