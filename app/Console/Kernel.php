<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\User;
use App\Models\Clock;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\RunScheduler::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $user = User::where('role', 'user')->get();
            foreach($user as $user){
                $clock = Clock::where('user_id', $user->uid)->latest()->first();
                if($clock->created_at != Carbon::yesterday()){
                    $user->absent += 1;
                    $user->save();
                }
            }
        })->weekdays()->daily();

        $schedule->call(function () {
            $user = User::where('role', 'user')->get();
            foreach($user as $user){
                $user->absent = 0;
                $user->save();
            }
        })->monthly();
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
