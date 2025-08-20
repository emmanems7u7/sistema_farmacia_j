<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ingresos</title>
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

<head>
    <meta charset="UTF-8">
    <title>Reporte de Ingresos</title>
    <style>
        body { font-family: Arial, sans-serif; }
        header { text-align: center; margin-bottom: 20px; }
        h1 { color: #2c3e50; font-size: 24px; }
        .fecha-rango { margin: 10px 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #3498db; color: white; text-align: left; padding: 8px; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .total { margin-top: 20px; text-align: right; font-weight: bold; font-size: 16px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <header>
        <h1>Reporte de Ingresos</h1>
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
                    <th class="text-right">Monto (Bs)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ingresos as $ingreso)
                    <tr>
                        <td>{{ $ingreso->created_at->format('d/m/Y') }}</td>
                        <td>{{ $ingreso->descripcion }}</td>
                        <td class="text-right">{{ number_format($ingreso->monto, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">No se encontraron ingresos en este período</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($ingresos->count() > 0)
            <div class="total">
                Total Ingresos: Bs {{ number_format($total, 2) }}
            </div>
        @endif

          <div class="footer">
        <p>Sistema de Gestión - {{ date('Y') }} </p>
        <p>Generado por: {{ Auth::user()->name ?? 'Sistema' }}</p>
    </div>
    </main>
</body>

</html>
