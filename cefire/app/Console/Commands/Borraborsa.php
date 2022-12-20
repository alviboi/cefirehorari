<?php

namespace App\Console\Commands;

use App\Http\Controllers\BorsaHoresController;
use Illuminate\Console\Command;

class Borraborsa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'borra:borsahores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $deute = new BorsaHoresController();
        $deute->borraborsa();
        return 0;
    }
}
