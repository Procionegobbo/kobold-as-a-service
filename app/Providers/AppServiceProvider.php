<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        RateLimiter::for('kobold-api', function (Request $request): Limit {
            if ($request->attributes->get('throttle_bypass') === true) {
                return Limit::none();
            }

            return Limit::perSecond(1)->by($request->ip());
        });
    }
}
