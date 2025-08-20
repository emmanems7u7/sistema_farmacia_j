<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Proveedor </title>
    <style>
        @page { 
            margin: 1.5cm; 
            size: A4 portrait; /* Cambiado a vertical */
        }
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10pt; 
            line-height: 1.5;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .header h1 { 
            color: #2c3e50; 
            font-size: 16pt;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 30px;
            font-size: 9pt;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
            color: #7f8c8d;
        }
        .logo {
            height: 60px;
            margin-bottom: 10px;
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
        <h1 class="compact-title">REPORTE DE PROVEEDORES</h1>
        <p class="compact-subtitle">Farmacia Mariel</p>
        <div class="compact-meta">
            {{ $fecha_generacion }} 
        </div>
    </div>
</div>

    <table>
        <thead>
            <tr>
                
                 <th>#</th>
                <th>Laboratorio</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Nombre del proveedor</th>
               
                <th>Celular</th>
                
           
            </tr>
        </thead>
         <tbody>
            @foreach($proveedores as $index => $proveedor)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $proveedor->empresa }}</td>
                 <td>{{ $proveedor->telefono ?? 'N/A' }}</td>
                   <td>{{ $proveedor->email ?? 'N/A' }}</td>
                <td>{{ $proveedor->nombre }}</td>
              
                <td>{{ $proveedor->celular ?? 'N/A' }}</td>
              
            </tr>
            @endforeach
        </tbody>
    </table>

     <div class="footer">
        <p>Sistema de Gestión - {{ date('Y') }} </p>
     
    </div>
</body>
</html>