<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSuperadmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Проверяем, что пользователь аутентифицирован и имеет роль superadmin
        if (!Auth::check() || !Auth::user()->hasRole('super_admin')) {
            return response('Access denied. You do not have permission to access this route.', 403);
        }

        return $next($request);
    }
}
