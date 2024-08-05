<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class tranSucursales extends Mailable
{
    use Queueable, SerializesModels;

    public $sucursal;
    public $fecha;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sucursal, $fecha)
    {
        //
        $this->sucursal = $sucursal;
        $this->fecha = $fecha;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.tranSucursales')
        ->from('alerta.web@sistegua.com')
        ->subject('Transferencia entre sucursales');
    }
}
