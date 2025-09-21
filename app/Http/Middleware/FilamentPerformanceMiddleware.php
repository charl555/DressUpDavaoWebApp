<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FilamentPerformanceMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Enable query caching for Filament routes
        if ($request->is('admin/*')) {
            // Reduce database queries by enabling query caching
            DB::enableQueryLog();
            
            // Set memory limit for large datasets
            ini_set('memory_limit', '256M');
            
            // Enable opcache if available
            if (function_exists('opcache_compile_file')) {
                opcache_compile_file(__FILE__);
            }
        }

        $response = $next($request);

        // Add performance headers for debugging
        if (config('app.debug') && $request->is('admin/*')) {
            $queries = DB::getQueryLog();
            $response->headers->set('X-Query-Count', count($queries));
            $response->headers->set('X-Memory-Usage', memory_get_peak_usage(true));
        }

        return $response;
    }
}
