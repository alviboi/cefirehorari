<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EliminarGuardia extends Mailable
{
    use Queueable, SerializesModels;

    public $subject="GUARDIA DEL CEFIRE VALENCIA BORRADA";

    public $dat=array();

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($datos)
    {
        //
        $this->dat=$datos;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.guardiaeliminada');
    }
}
