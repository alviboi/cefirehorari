<?php

namespace App\Console\Commands;
use App\Http\Controllers\HorariespecialController;


use Illuminate\Console\Command;

class borrahorariespecial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'borra:horariespecial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borra horaries especials de la taula horariesespecials de la base de dades cefire';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        #Borra taula horari especial
        $horariespecial = new HorariespecialController();
        $horariespecial->borra_horari_especial();
        return 0;
    }
}
