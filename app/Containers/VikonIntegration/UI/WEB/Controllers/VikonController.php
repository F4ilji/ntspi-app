<?php

namespace App\Containers\VikonIntegration\UI\WEB\Controllers;

use App\Containers\VikonIntegration\Actions\Auth\AuthenticateAction;
use App\Containers\VikonIntegration\Actions\CheckAccessAction;
use App\Containers\VikonIntegration\Actions\CheckVersionAction;
use App\Containers\VikonIntegration\Actions\UpdateCoreAction;
use App\Containers\VikonIntegration\Tasks\RefreshTokenTask;
use App\Containers\VikonIntegration\Tasks\ValidateTokenTask;
use App\Containers\VikonIntegration\UI\WEB\Requests\AuthenticateRequest;
use App\Containers\VikonIntegration\UI\WEB\Requests\UpdateModuleRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class VikonController extends Controller
{
    public function __construct(
        private readonly AuthenticateAction $auth,
        private readonly CheckAccessAction $checkAccess,
        private readonly CheckVersionAction $checkVersion,
        private readonly UpdateCoreAction $updateCore,
        private readonly RefreshTokenTask $refreshToken,
        private readonly ValidateTokenTask $validateToken,
    ) {}

    public function index(): \Inertia\Response
    {
        $token = Session::get('vikon_access_token');
        $isAuth = $token ? $this->validateToken->run($token) : false;

        return inertia()->render('Dashboard/VikonUpdates/Index', [
            'is_authenticated' => $isAuth,
            'current_version' => config('vikon.current_version'),
            'modules' => config('vikon.modules'),
            'vikon_api_domain' => config('vikon.api_domain'),
            'vikon_client_id' => config('vikon.client_id'),
        ]);
    }

    public function oauthCallback(Request $request): \Inertia\Response
    {
        $state = $request->query('state');
        $expectedState = Session::pull('oauth_state');

        if ($state && $expectedState && $state !== $expectedState) {
            Log::warning('Vikon OAuth CSRF mismatch');
        }

        $code = $request->query('code');
        if ($code) {
            try {
                $redirectUri = route('dashboard.vikon-updates.callback');
                $tokens = $this->auth->run($code, $redirectUri);
                Session::put('vikon_access_token', $tokens['access_token']);
                Session::put('vikon_refresh_token', $tokens['refresh_token']);
            } catch (\Throwable $e) {
                Log::error('OAuth callback failed', ['error' => $e->getMessage()]);
            }
        }

        $token = Session::get('vikon_access_token');
        $isAuth = $token ? $this->validateToken->run($token) : false;

        return inertia()->render('Dashboard/VikonUpdates/Index', [
            'is_authenticated' => $isAuth,
            'current_version' => config('vikon.current_version'),
            'modules' => config('vikon.modules'),
            'vikon_api_domain' => config('vikon.api_domain'),
            'vikon_client_id' => config('vikon.client_id'),
        ]);
    }

    public function authenticate(AuthenticateRequest $request): JsonResponse
    {
        try {
            $tokens = $this->auth->run(
                $request->validated('code'),
                $request->validated('redirect_uri')
            );
            Session::put('vikon_access_token', $tokens['access_token']);
            Session::put('vikon_refresh_token', $tokens['refresh_token']);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Ошибка авторизации'], 422);
        }
    }

    public function refreshToken(): JsonResponse
    {
        $refreshToken = Session::get('vikon_refresh_token');
        if (!$refreshToken) {
            return response()->json(['success' => false, 'message' => 'Нет refresh токена'], 401);
        }

        try {
            $tokens = $this->refreshToken->run($refreshToken);
            Session::put('vikon_access_token', $tokens['access_token']);
            Session::put('vikon_refresh_token', $tokens['refresh_token']);
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Session::forget(['vikon_access_token', 'vikon_refresh_token']);
            return response()->json(['success' => false, 'message' => 'Токен истёк'], 401);
        }
    }

    public function checkAccess(): JsonResponse
    {
        $token = Session::get('vikon_access_token');
        if (!$token) {
            return response()->json(['success' => false, 'requires_auth' => true], 401);
        }

        $result = $this->checkAccess->run($token);
        return response()->json($result);
    }

    public function checkVersion(): JsonResponse
    {
        $token = Session::get('vikon_access_token');
        if (!$token) {
            return response()->json(['success' => false, 'requires_auth' => true], 401);
        }

        return response()->json($this->checkVersion->run($token));
    }

    public function updateModule(UpdateModuleRequest $request): JsonResponse
    {
        $token = Session::get('vikon_access_token');
        if (!$token) {
            return response()->json(['success' => false, 'requires_auth' => true], 401);
        }

        try {
            $message = $this->updateCore->run($request->validated('module_id'), $token);
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Ошибка обновления'], 500);
        }
    }

    public function logout(): JsonResponse
    {
        Session::forget(['vikon_access_token', 'vikon_refresh_token']);
        return response()->json(['success' => true]);
    }

    public function getAuthUrl(): JsonResponse
    {
        $state = \Illuminate\Support\Str::random(32);
        Session::put('oauth_state', $state);

        $redirectUri = route('dashboard.vikon-updates.callback');
        $url = config('vikon.auth_domain') . 'oauth2/authorize'
            . '?client_id=' . config('vikon.client_id')
            . '&redirect_uri=' . urlencode($redirectUri)
            . '&response_type=code'
            . '&state=' . $state;

        return response()->json(['url' => $url]);
    }
}
