<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $pendingApprovalCount = \App\Models\User::where('session_status', 'pending')
                ->where('needs_approval', true)
                ->where('role', 'user')
                ->count();
    
            $view->with('pendingApprovalCount', $pendingApprovalCount);
        });
        // Keep your existing scheduled command
        $this->app->booted(function () {
            $this->app->make(\Illuminate\Console\Scheduling\Schedule::class)
                ->command('members:update-status')
                ->everyMinute();
        });
    }
}