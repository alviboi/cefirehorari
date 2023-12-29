<?php

namespace App\Console\Commands;

use App\Http\Controllers\VacancespendentsController;

use Illuminate\Console\Command;

class vacancesmarc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vacances:marc';

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
        $deute = new VacancespendentsController();
        $deute->marc();
        return 0;
    }
}
