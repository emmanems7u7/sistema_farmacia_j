<!DOCTYPE html>
<html>
<head>
    <title>Productos Vencidos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #f44336; color: white; }
    </style>
</head>
<body>
    <h2>Reporte de Productos Vencidos</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Fecha de Vencimiento</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $producto)
                <tr>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->categoria->nombre ?? 'N/A' }}</td>
                    <td>{{ $producto->fecha_vencimiento }}</td>
                    <td>{{ $producto->cantidad }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
