<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SolicitudGasto extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $fecha;
    public $descripcion;
    public $estado;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($usuario,$fecha,$descripcion,$estado)
    {
        $this->usuario      = $usuario;
        $this->fecha        = $fecha;
        $this->descripcion  = $descripcion;;
        $this->estado       = $estado;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.solicitudGasto')
        ->from('alerta.web@sistegua.com')
        ->subject('Solicitud de autorizacion de gasto');
    }
}
