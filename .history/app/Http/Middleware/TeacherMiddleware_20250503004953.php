<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TeacherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check teacher guard authentication
        if (!auth()->guard('teacher')->check()) {
            return response()->json(['message' => 'Unauthorized. Teacher access required.'], 401);
        }

        // Check teacher role
        if (!auth()->guard('teacher')->user()->hasRole('teacher', 'teacher')) {
            return response()->json(['message' => 'Forbidden. Teacher privileges required.'], 403);
        }

        return $next($request);
    }
}
