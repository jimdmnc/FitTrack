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
            // Count pending approvals
            $pendingApprovalCount = User::where('session_status', 'pending')
                ->where('needs_approval', true)
                ->where(function($query) {
                    $query->where('role', 'user')
                          ->orWhere('role', 'userSession');
                })
                ->count();
    
            // Count rejected approvals
            $rejectedApprovalCount = User::where('session_status', 'rejected')
                ->where('needs_approval', true)
                ->where(function($query) {
                    $query->where('role', 'user')
                          ->orWhere('role', 'userSession');
                })
                ->count();
    
            // Share both counts to all views
            $view->with([
                'pendingApprovalCount' => $pendingApprovalCount,
                'rejectedApprovalCount' => $rejectedApprovalCount,
            ]);
        });
    
        // Keep your existing scheduled command
        $this->app->booted(function () {
            $this->app->make(\Illuminate\Console\Scheduling\Schedule::class)
                ->command('members:update-status')
                ->everyMinute();
        });
    }
    
}