<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\PDF; // Si usas DomPDF para generar PDF

class ReporteProductosVencidos extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf; // PDF que vamos a adjuntar

    /**
     * Create a new message instance.
     */
    public function __construct(PDF $pdf)
    {
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Reporte de Productos Vencidos')
                    ->view('emails.productos_vencidos') // vista simple de correo
                    ->attachData($this->pdf->output(), 'productos_vencidos.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
