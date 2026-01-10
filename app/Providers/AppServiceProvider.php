<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
    public function boot(): void
    {
        // Configure rate limiting for PSI API
        RateLimiter::for('psi', function ($job) {
            return Limit::perMinute(config('services.psi.requests_per_minute', 400));
        });

        // Gate for Horizon access - only admins
        Gate::define('viewHorizon', function ($user) {
            return $user->is_admin;
        });
    }
}
