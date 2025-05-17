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
        if (!Auth::guard('teacher')->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Additional role check if needed
        if (!Auth::guard('teacher')->user()->hasAnyRole(['admin', 'teacher'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
