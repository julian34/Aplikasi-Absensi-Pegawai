<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinalCorsCleanup
{
    /**
     * Final CORS header cleanup - runs after all other middleware and application code.
     * This middleware ensures only the specific origin is in the Access-Control-Allow-Origin header,
     * not a wildcard, which is required for requests with credentials mode.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Only process API requests
        if (!$request->is('api/*')) {
            return $response;
        }
        
        // Try multiple methods to completely remove Access-Control-Allow-Origin headers
        
        // Method 1: Clear the header bag completely and rebuild
        $headers = $response->headers;
        
        // Get all current headers
        $allHeaders = $headers->all();
        
        // If Access-Control-Allow-Origin exists, remove all instances
        if (isset($allHeaders['Access-Control-Allow-Origin'])) {
            // Remove via the HeaderBag method
            $headers->remove('Access-Control-Allow-Origin');
            
            // Double-check with native header_remove
            header_remove('Access-Control-Allow-Origin');
        }
        
        // Method 2: Set with replace=true using native PHP (override any framework value)
        // Use ini_set to ensure headers are set properly
        header('Access-Control-Allow-Origin: http://localhost:5174', true);
        header('Access-Control-Allow-Credentials: true', true);
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS', true);
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, X-CSRF-TOKEN', true);
        
        // Method 3: Also set via Symfony response headers as backup
        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5174');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        
        // Method 4: Try to clear response body to see if that affects header parsing
        // (probably won't help but worth trying)
        
        return $response;
    }
}

