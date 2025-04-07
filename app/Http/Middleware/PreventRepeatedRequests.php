<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PreventRepeatedRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'processing_request_' . ($request->user()->id ?? Auth::guard('institute')->user()->id);

        // Check if a request is already being processed
        if (Cache::has($key)) {
            return response()->json('Request is already in process', 429);
        }

        // Set cache with a timeout (e.g., 30 seconds)
        Cache::put($key, true, 10);

        $response = $next($request);

        // Remove the cache key after processing
        Cache::forget($key);

        return $response;
    }
}
