<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomCorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5174');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-TOKEN, Authorization, X-Requested-With');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        // Handle preflight OPTIONS request
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200)->withHeaders([
                'Access-Control-Allow-Origin' => 'http://localhost:5174',
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, X-CSRF-TOKEN, Authorization, X-Requested-With',
                'Access-Control-Allow-Credentials' => 'true',
            ]);
        }

        return $response;
    }
}