<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Transferencia extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $fecha;
    public $estado;
    public $numero;
    public $correo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($usuario,$fecha,$estado,$numero,$correo)
    {
        //
        $this->usuario = $usuario;
        $this->fecha = $fecha;
        $this->estado = $estado;
        $this->numero = $numero;
        $this->correo = $correo;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.transferencia')
        ->from('alerta.web@sistegua.com')
        ->subject('Transferencia entre bodegas');
    }
}
