<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Always use the specific frontend origin
        $origin = 'http://localhost:5174';
        
        // Set CORS headers using native PHP function (must be before any output)
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, X-CSRF-TOKEN');
        header('Access-Control-Max-Age: 86400');
        
        // For OPTIONS requests, return early
        if ($request->isMethod('OPTIONS')) {
            return response()->json([]);
        }
        
        // Get the response from the next middleware/controller
        $response = $next($request);
        
        return $response;
    }
}
