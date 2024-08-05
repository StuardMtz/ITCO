<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InventarioFinalizado extends Mailable
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
        return $this->view('mails.Finalizado')
        ->from('alerta.web@sistegua.com')
        ->subject('Inventario Finalizado');
    }
}
