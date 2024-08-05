<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Sucursal extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $fecha;
    public $estado;
    public $numero;

    /**
     * 
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($usuario,$fecha,$estado,$numero)
    {
        //
        $this->usuario = $usuario;
        $this->fecha = $fecha;
        $this->estado = $estado;
        $this->numero = $numero;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.notiSucursal')
        ->from('alerta.web@sistegua.com')
        ->subject('Transferencia entre bodegas');
    }
}
