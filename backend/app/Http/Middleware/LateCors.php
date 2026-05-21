<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LateCors
{
    /**
     * Set CORS headers using multiple approaches to ensure wildcard is completely removed.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Method 1: Remove via Symfony's HeaderBag
        $response->headers->remove('Access-Control-Allow-Origin');
        
        // Method 2: Use header_remove() to remove native PHP headers
        header_remove('Access-Control-Allow-Origin');
        
        // Method 3: Set via Symfony response object with 'replace' flag
        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5174');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, X-CSRF-TOKEN');
        $response->headers->set('Access-Control-Max-Age', '86400');
        
        // Method 4: Use native header() with replace flag LAST to ensure it wins
        header('Access-Control-Allow-Origin: http://localhost:5174', true);
        header('Access-Control-Allow-Credentials: true', true);
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS', true);
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, X-CSRF-TOKEN', true);
        header('Access-Control-Max-Age: 86400', true);
        
        return $response;
    }
}
