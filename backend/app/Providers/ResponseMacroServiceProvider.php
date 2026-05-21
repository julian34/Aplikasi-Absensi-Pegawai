<?php

namespace App\Providers;

use Closure;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Response as HttpResponse;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register a global after middleware to clean CORS headers at the very end
        $this->app[\Illuminate\Contracts\Http\Kernel::class]->pushMiddleware(
            FinalCorsCleanup::class
        );
    }
}

// Create inline middleware class
class FinalCorsCleanup
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        if ($request->is('api/*')) {
            // Remove ALL Access-Control-Allow-Origin headers
            $response->headers->remove('Access-Control-Allow-Origin');
            
            // Set ONLY the specific origin
            $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5174');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }
        
        return $response;
    }
}
