<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Bind the schedule after application is fully booted
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('members:update-status')->daily();
        });
    }
}
