<?php

namespace App\Console;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\Calculadeute::class,
        Commands\Borraborsa::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command("calcula:mesdeute")->monthly(); //Run the task on the first day of every month at 00:00
        /*
        Quan es vullga implementar que es borre també el deute del mes s'ha d'afegir açí
        */
        
        //$schedule->command("borra:deutemes")->yearlyOn(9, 1, '00:05');

        $schedule->command("borra:borsahores")->yearlyOn(8, 31, '00:01');
        $schedule->command("vacances:gener")->yearlyOn(1, 1, '00:01');
        $schedule->command("vacances:marc")->yearlyOn(2, 15, '00:01');


        //$schedule->command("calcula:mesdeute")->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
