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
        // Add the View Composer for pending approvals
        View::composer('*', function ($view) {
            $pendingApprovalCount = cache()->remember(
                'pending_approval_count', 
                now()->addMinutes(5), 
                function () {
                    return User::where('session_status', 'pending')->count();
                }
            );
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