<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Categorías</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { 
            text-align: center; 
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .logo { max-width: 150px; }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        .table td {
            padding: 8px;
            border: 1px solid #dee2e6;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
            color: #6c757d;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Reporte de Categorías</h2>
        <p>Generado el: {{ $fecha_generacion }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Descripción</th>
              
            </tr>
        </thead>
        <tbody>
            @foreach ($categorias as $categoria)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $categoria->nombre }}</td>
                <td>{{ $categoria->descripcion ?? 'N/A' }}</td>
              
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema de Gestión - {{ config('app.name') }}</p>
    </div>

    <!-- Solo para vista de impresión -->
    
</body>
</html>