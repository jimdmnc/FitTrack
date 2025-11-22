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
            $pendingApprovalCount = User::where('session_status', 'pending')
                ->where('needs_approval', true)
                ->where(function($query) {
                    $query->where('role', 'user')
                          ->orWhere('role', 'userSession');
                })
                ->count();
    
            // Calculate expiring members (7 days before expiration)
            $sevenDaysFromNow = now()->addDays(7)->format('Y-m-d');
            $today = now()->format('Y-m-d');
            
            $expiringMembers = User::where('role', 'user')
                ->whereNotNull('end_date')
                ->where('end_date', '>=', $today)
                ->where('end_date', '<=', $sevenDaysFromNow)
                ->where('member_status', '!=', 'expired')
                ->orderBy('end_date', 'asc')
                ->get(['id', 'first_name', 'last_name', 'end_date', 'rfid_uid', 'email']);
            
            $expiringMembersCount = $expiringMembers->count();
            
            // Calculate expired members
            $expiredMembers = User::where('role', 'user')
                ->where(function($query) use ($today) {
                    $query->where('member_status', 'expired')
                          ->orWhere(function($q) use ($today) {
                              $q->whereNotNull('end_date')
                                ->where('end_date', '<', $today);
                          });
                })
                ->orderBy('end_date', 'desc')
                ->get(['id', 'first_name', 'last_name', 'end_date', 'rfid_uid', 'email']);
            
            $expiredMembersCount = $expiredMembers->count();
    
            $view->with('pendingApprovalCount', $pendingApprovalCount);
            $view->with('expiringMembersCount', $expiringMembersCount);
            $view->with('expiringMembers', $expiringMembers);
            $view->with('expiredMembersCount', $expiredMembersCount);
            $view->with('expiredMembers', $expiredMembers);
        });
    
        // Keep your existing scheduled command
        $this->app->booted(function () {
            $this->app->make(\Illuminate\Console\Scheduling\Schedule::class)
                ->command('members:update-status')
                ->everyMinute();
        });
    }
}