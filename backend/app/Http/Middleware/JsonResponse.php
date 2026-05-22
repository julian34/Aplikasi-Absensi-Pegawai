<?php

namespace App\Http\Middleware;

// This middleware ensures that all responses from API routes have the Content-Type header set to application/json.
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// fungsi handle menerima request dan closure $next yang akan memproses request selanjutnya. Setelah mendapatkan response dari $next, middleware ini akan memeriksa apakah request berasal dari route yang dimulai dengan 'api/'. Jika ya, maka header Content-Type pada response akan diatur menjadi application/json sebelum dikembalikan ke client.
class JsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    // Fungsi handle menerima request dan closure $next yang akan memproses request selanjutnya. Setelah mendapatkan response dari $next, middleware ini akan memeriksa apakah request berasal dari route yang dimulai dengan 'api/'. Jika ya, maka header Content-Type pada response akan diatur menjadi application/json sebelum dikembalikan ke client.
    public function handle(Request $request, Closure $next): Response
    {
        // Proses request dan dapatkan response
        $response = $next($request);
        
        // Set JSON content type for all API responses
        if ($request->is('api/*')) {
            // Pastikan response adalah instance dari Response sebelum mengatur header
            $response->header('Content-Type', 'application/json');
        }
        
        return $response;
    }
}
