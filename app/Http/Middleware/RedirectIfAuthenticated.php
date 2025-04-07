<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     * string ...$guards
    * // $guards = empty($guards) ? [null] : $guards;
    *   // foreach ($guards as $guard) {
    *   //     if (Auth::guard($guard)->check()) {
    *   //         return redirect(RouteServiceProvider::HOME);
    *   //     }
    *  // }
    *   // return $next($request);
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard = null): Response
    {
        if (Auth::guard('web')->check()) {
            // User is authenticated with the default guard
            return redirect(RouteServiceProvider::HOME);
        } elseif (Auth::guard('student')->check()) {
            // User is authenticated with the student guard
            return redirect()->route('student.dashboard');
        } elseif (Auth::guard('institute')->check()) {
            // User is authenticated with the institute guard
            return redirect()->route('institute.dashboard');
        }

        return $next($request);
    }
}
