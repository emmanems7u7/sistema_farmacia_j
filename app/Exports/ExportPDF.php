<?php

namespace App\Exports;

use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Response;

class ExportPDF
{
    public static function exportPdf(
        string $view,
        array $data,
        string $filenameBase,
        bool $download = true,
        array $mpdfConfig = []
    ): Response {
        $html = View::make($view, $data)->render();

        $mpdf = new Mpdf($mpdfConfig);

        $mpdf->WriteHTML($html);

        $filename = $filenameBase . '_' . now()->format('Y-m-d_H-i') . '.pdf';

        // Elegir modo de salida según $download
        $destination = $download
            ? \Mpdf\Output\Destination::DOWNLOAD
            : \Mpdf\Output\Destination::INLINE;

        // Generar PDF y obtener contenido como string o dejar que mPDF maneje la respuesta
        if ($download) {
            // Salida directa para descarga
            return response(
                $mpdf->Output($filename, $destination)
            )
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "attachment; filename={$filename}");
        } else {
            // Para vista inline, mPDF ya envía las cabeceras, solo retornamos el contenido bruto
            // o puedes devolver una respuesta con contenido para mostrar inline
            $pdfContent = $mpdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN);

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "inline; filename={$filename}");
        }
    }
}
