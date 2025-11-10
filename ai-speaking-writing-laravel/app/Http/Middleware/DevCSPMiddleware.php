<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DevCSPMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (app()->environment('local')) {
            // Cho phép tất cả nguồn bên ngoài khi dev
            $response->headers->set(
                'Content-Security-Policy',
                "default-src * 'unsafe-inline' 'unsafe-eval' data: blob:;"
            );
        }

        return $response;
    }
}
