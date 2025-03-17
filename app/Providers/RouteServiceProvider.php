<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        $this->configureRateLimiting();
    
        $this->routes(function () {
            // Load the default api.php file
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
    
            // Load your custom routes file (if needed)
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/custom_api.php')); // Replace with your file name
        });
    }
}
