<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Mail\Incidenciaresolta;
use Illuminate\Support\Facades\Mail;


class SendIncidenciaresolta implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $datos;
    public $mail_env;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mail2, $datos2)
    {
        //
        $this->datos = $datos2;
        $this->mail_env = $mail2;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //$guardia->user['email']
        Mail::to($this->mail_env)->send(new Incidenciaresolta($this->datos));

    }
}