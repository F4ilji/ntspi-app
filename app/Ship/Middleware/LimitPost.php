<?php

namespace App\Ship\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LimitPost
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->receivedInvitation === null) {
            return $next($request);
        }
        if (auth()->user()->receivedInvitation->post_limit > 0) {
            return $next($request);
        } else {
            throw new HttpException(403, 'Лимит постов исчерпан');
        }
    }
}
