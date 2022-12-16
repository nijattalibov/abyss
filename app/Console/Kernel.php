<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Person;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $persons = Person::orderBy('created_at', 'DESC')->get();
            $timeSecond = strtotime(date("Y-m-d H:i:s"));
            foreach ($persons as $item){
                $timeFirst  = strtotime($item["created_at"]);
                $differenceInSeconds = $timeSecond - $timeFirst;
                if($differenceInSeconds>=2592000){
                    $item->delete();
                } 
            }  
        })->hourly()->timezone('Asia/Baku');
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
