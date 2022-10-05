<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Mail\Nou_usuari_ldap;
use Illuminate\Support\Facades\Mail;

class SendPasswordMail implements ShouldQueue
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
        //
        Mail::to($this->mail)->send(new Nou_usuari_ldap($this->datos));
    }
}
