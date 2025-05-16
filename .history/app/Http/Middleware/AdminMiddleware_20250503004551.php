<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check admin guard authentication
        if (!auth()->guard('admin')->check()) {
            return response()->json(['message' => 'Unauthorized. Admin access required.'], 401);
        }

        // Check admin role
        if (!auth()->guard('admin')->user()->hasRole('admin', 'admin')) {
            return response()->json(['message' => 'Forbidden. Insufficient privileges.'], 403);
        }

        return $next($request);
    }
}
