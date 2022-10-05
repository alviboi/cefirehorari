<?php
//Mail::to($guardia->user['email'])->send(new EliminarGuardia($datos2));
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Mail\EliminarGuardia;
use Illuminate\Support\Facades\Mail;

class SendGuardiaBorrarMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $datos;
    protected $mail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mail,$datos)
    {
        //
        $this->datos = $datos;
        $this->mail = $mail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //$guardia->user['email']
        //Mail::to($this->mail)->send(new EnviarGuardia($this->datos));
        Mail::to($this->mail)->send(new EliminarGuardia($this->datos));

    }
}
