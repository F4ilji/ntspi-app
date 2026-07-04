<?php

namespace App\Http\Middleware;

use App\Containers\VikonIntegration\Tasks\RefreshTokenTask;
use App\Containers\VikonIntegration\Tasks\ValidateTokenTask;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class VikonTokenRefresh
{
    public function handle(Request $request, Closure $next)
    {
        $token = Session::get('vikon_access_token');
        $refreshToken = Session::get('vikon_refresh_token');

        if ($token && $refreshToken) {
            $validate = app(ValidateTokenTask::class);

            if (!$validate->run($token)) {
                try {
                    $tokens = app(RefreshTokenTask::class)->run($refreshToken);
                    Session::put('vikon_access_token', $tokens['access_token']);
                    Session::put('vikon_refresh_token', $tokens['refresh_token']);
                } catch (\Throwable $e) {
                    Log::warning('Vikon auto-refresh failed', ['error' => $e->getMessage()]);
                    Session::forget(['vikon_access_token', 'vikon_refresh_token']);
                }
            }
        }

        return $next($request);
    }
}
