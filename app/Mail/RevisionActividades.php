<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RevisionActividades extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $fecha;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($usuario,$fecha)
    {
        $this->usuario      = $usuario;
        $this->fecha        = $fecha;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.RevisionActividad')
        ->from('alerta.web@sistegua.com')
        ->subject('Actividad diaria revisada');
    }
}
