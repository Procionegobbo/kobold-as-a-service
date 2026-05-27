<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThrottleBypassMiddleware
{
    /**
     * Skip the kobold-api throttle when the request carries a valid bypass key.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bypassers = config('app.throttle_bypassers', []);

        if (
            $bypassers !== []
            && in_array($request->header('X-Bypass-Key'), $bypassers, strict: true)
        ) {
            $request->attributes->set('throttle_bypass', true);
        }

        return $next($request);
    }
}
