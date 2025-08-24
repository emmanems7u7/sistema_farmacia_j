<?php

namespace App\Http\Controllers;
use App\Models\MovimientoCaja;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DatePeriod; // Importación añadida
use DateInterval; // Importación añadida
use DateTime; // Importación añadida
// Asegúrate de tener este modelo para acceder a los datos de ingresos
use PDF; // Si estás usando domPDF o cualquier paquete para generar PDF

class ReporteController extends Controller
{
    // Método para mostrar la vista con el formulario de fechas
    public function reporteIngresosView()
    {

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'reportes', 'url' => route('admin.reporte.ingresos.index')],
        ];
        return view('admin.reporte.ingresos', compact('breadcrumb')); // Esto buscará la vista en resources/views/reporte/ingresos.blade.php
    }

    public function ingresosPorFecha(Request $request)
    {

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'reportes', 'url' => route('admin.reporte.ingresos.index')],
            ['name' => 'reporte por fecha', 'url' => route('admin.reporte.ingresos.index')],

        ];

        // Validación de fechas
        $validated = $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        // Ajustar fechas para incluir todo el día
        $fecha_inicio = Carbon::parse($validated['fecha_inicio'])->startOfDay();
        $fecha_fin = Carbon::parse($validated['fecha_fin'])->endOfDay();

        $dias_rango = $fecha_inicio->diffInDays($fecha_fin);

        if ($dias_rango <= 30) {
            // Agrupación por día con Carbon para mayor precisión
            $resultados = MovimientoCaja::where('tipo', 'INGRESO')
                ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
                ->get()
                ->groupBy(function ($item) {
                    return $item->created_at->format('Y-m-d');
                });

            // Generar rango de fechas completo
            $periodo = new DatePeriod(
                $fecha_inicio->toDateTime(),
                new DateInterval('P1D'),
                $fecha_fin->toDateTime()->modify('+1 day')
            );

            $datos = [];
            $labels = [];

            foreach ($periodo as $fecha) {
                $fecha_formato = $fecha->format('Y-m-d');
                $labels[] = $fecha->format('d M'); // Formato más legible (05 Jun)
                $monto = isset($resultados[$fecha_formato]) ?
                    $resultados[$fecha_formato]->sum('monto') : 0;
                $datos[] = $monto;
            }
        } else {
            // Agrupación por mes
            $resultados = MovimientoCaja::where('tipo', 'INGRESO')
                ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
                ->get()
                ->groupBy(function ($item) {
                    return $item->created_at->format('Y-m');
                });

            // Rellenar meses sin movimientos
            $fechaIni = $fecha_inicio->copy()->startOfMonth();
            $fechaFin = $fecha_fin->copy()->endOfMonth();

            $datos = [];
            $labels = [];

            while ($fechaIni <= $fechaFin) {
                $mes = $fechaIni->format('Y-m');
                $labels[] = $fechaIni->format('M Y'); // Formato más legible (Jun 2023)
                $monto = isset($resultados[$mes]) ?
                    $resultados[$mes]->sum('monto') : 0;
                $datos[] = $monto;
                $fechaIni->addMonth();
            }
        }

        $total = array_sum($datos);

        return view('admin.reporte.ingresos', compact('labels', 'datos', 'total', 'breadcrumb'));
    }

    public function ingresosPorFechaPDF(Request $request)
    {
        $validated = $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        $fechaInicio = Carbon::parse($validated['fecha_inicio'])->startOfDay();
        $fechaFin = Carbon::parse($validated['fecha_fin'])->endOfDay();

        // FILTRAR SOLO INGRESOS
        $ingresos = MovimientoCaja::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('tipo', 'INGRESO') // Este filtro es crucial
            ->orderBy('created_at')
            ->get();

        $total = $ingresos->sum('monto');

        $pdf = Pdf::loadView('admin.reporte.ingresos_por_fecha_pdf', [
            'ingresos' => $ingresos,
            'total' => $total,
            'fechaInicio' => $fechaInicio->format('d/m/Y'),
            'fechaFin' => $fechaFin->format('d/m/Y')
        ]);

        return $pdf->stream('reporte_ingresos.pdf');
    }


    //EGRESOS 

    public function reporteEgresosView()
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Reporte Egresos', 'url' => route('admin.reporte.ingresos.index')],
        ];
        return view('admin.reporte.egresos', compact('breadcrumb')); // Esto buscará la vista en resources/views/reporte/ingresos.blade.php
    }

    public function egresosPorFecha(Request $request)
    {

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Reporte Egresos', 'url' => route('admin.reporte.ingresos.index')],
            ['name' => 'Reporte Egresos por Fecha', 'url' => route('admin.reporte.ingresos.index')],

        ];
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');

        // Convertir a Carbon y ajustar horas
        $startDate = Carbon::parse($fecha_inicio)->startOfDay();
        $endDate = Carbon::parse($fecha_fin)->endOfDay();

        // Obtener egresos en el rango (más eficiente)
        $egresos = MovimientoCaja::where('tipo', 'EGRESO')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $dias_rango = $startDate->diffInDays($endDate);

        $datos = [];
        $labels = [];

        if ($dias_rango <= 30) {
            // Agrupar por día
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                $fecha_formato = $currentDate->format('Y-m-d');
                $dia_semana = $currentDate->isoFormat('dddd');

                // Filtrar usando Carbon para precisión
                $monto = $egresos->filter(function ($egreso) use ($currentDate) {
                    return $egreso->created_at->isSameDay($currentDate);
                })->sum('monto');

                // Formato más amigable para las etiquetas
                $labels[] = $currentDate->format('d M'); // Ej: "05 Jun"
                $datos[] = $monto;

                $currentDate->addDay();
            }
        } else {
            // Agrupar por mes (código existente)
            // ... (mantener tu lógica actual para meses)
        }

        $total = array_sum($datos);

        return view('admin.reporte.egresos', compact('labels', 'datos', 'total', 'breadcrumb'));
    }


    public function EgresosPorFechaPDF(Request $request)
    {
        $validated = $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        $fechaInicio = Carbon::parse($validated['fecha_inicio'])->startOfDay();
        $fechaFin = Carbon::parse($validated['fecha_fin'])->endOfDay();

        // FILTRAR SOLO INGRESOS
        $egresos = MovimientoCaja::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('tipo', 'EGRESO') // Este filtro es crucial
            ->orderBy('created_at')
            ->get();

        $total = $egresos->sum('monto');

        $pdf = Pdf::loadView('admin.reporte.egresos_por_fecha_pdf', [
            'egresos' => $egresos,
            'total' => $total,
            'fechaInicio' => $fechaInicio->format('d/m/Y'),
            'fechaFin' => $fechaFin->format('d/m/Y')
        ]);

        return $pdf->stream('reporte_egresos.pdf');
    }





}
