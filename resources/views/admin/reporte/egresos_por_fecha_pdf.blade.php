<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Egresos</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 40px;
        }
        header {
            text-align: center;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 24px;
            margin: 0;
            color: #0056b3;
        }
        .fecha-rango {
            text-align: center;
            font-size: 14px;
            margin-top: 5px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        thead {
            background-color: #0056b3;
            color: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tfoot {
            margin-top: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
            color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1>Reporte de Egresos</h1>
        <div class="fecha-rango">
            <strong>Desde:</strong> {{ $fechaInicio }} &nbsp; | &nbsp;
            <strong>Hasta:</strong> {{ $fechaFin }}
        </div>
    </header>

    <main>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Monto (Bs)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($egresos as $egreso)
                    <tr>
                        <td>{{ $egreso->created_at->format('Y-m-d') }}</td>
                        <td>{{ $egreso->descripcion }}</td>
                        <td>{{ number_format($egreso->monto, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            Total Egresos: Bs {{ number_format($total, 2) }}
        </div>


                  <div class="footer">
        <p>Sistema de Gestión - {{ date('Y') }} </p>
        <p>Generado por: {{ Auth::user()->name ?? 'Sistema' }}</p>
    </div>
    </main>
</body>
</html>
