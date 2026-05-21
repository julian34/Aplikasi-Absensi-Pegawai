<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CleanupCors
{
    /**
     * Set correct CORS headers for API requests.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set CORS headers BEFORE the response is created by using native header function
        header_remove('Access-Control-Allow-Origin');
        header('Access-Control-Allow-Origin: http://localhost:5174');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, X-CSRF-TOKEN');
        header('Access-Control-Max-Age: 86400');
        
        // For OPTIONS preflight, return empty response
        if ($request->isMethod('OPTIONS')) {
            return response()->json([]);
        }
        
        $response = $next($request);
        
        return $response;
    }
}
