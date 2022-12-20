<?php

namespace App\Console\Commands;

use App\Http\Controllers\DeutesmesController;
use Illuminate\Console\Command;

class Calculadeute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calcula:mesdeute';

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
        $deute = new DeutesmesController();
        $deute->calcula_deutes_mes();
        return 0;
    }
}
