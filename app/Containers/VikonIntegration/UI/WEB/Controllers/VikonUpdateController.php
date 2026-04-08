<?php

namespace App\Containers\VikonIntegration\UI\WEB\Controllers;

use App\Containers\VikonIntegration\Actions\Auth\AuthenticateVikonAction;
use App\Containers\VikonIntegration\Actions\Updates\CheckVikonAccessAction;
use App\Containers\VikonIntegration\Actions\Updates\CheckVikonVersionAction;
use App\Containers\VikonIntegration\Actions\Updates\DownloadModuleUpdateAction;
use App\Containers\VikonIntegration\Actions\Updates\SyncModuleFilesAction;
use App\Containers\VikonIntegration\Tasks\CheckVikonEntryPointTask;
use App\Containers\VikonIntegration\Tasks\RefreshVikonTokenTask;
use App\Containers\VikonIntegration\Tasks\ValidateVikonTokenTask;
use App\Containers\VikonIntegration\UI\WEB\Requests\AuthenticateVikonRequest;
use App\Containers\VikonIntegration\UI\WEB\Requests\DownloadModuleUpdateRequest;
use App\Containers\VikonIntegration\UI\WEB\Requests\RefreshVikonTokenRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VikonUpdateController extends Controller
{
    public function __construct(
        private readonly AuthenticateVikonAction $authenticateAction,
        private readonly CheckVikonAccessAction $checkAccessAction,
        private readonly CheckVikonVersionAction $checkVersionAction,
        private readonly DownloadModuleUpdateAction $downloadUpdateAction,
        private readonly SyncModuleFilesAction $syncFilesAction,
        private readonly CheckVikonEntryPointTask $checkEntryPointTask,
        private readonly RefreshVikonTokenTask $refreshTokenTask,
        private readonly ValidateVikonTokenTask $validateTokenTask,
    ) {}

    /**
     * Get current version from config
     */
    private function getCurrentVersion(): string
    {
        return config('vikon.current_version', file_get_contents(base_path('vikon_version.txt')) ?: '1.0.0');
    }

    /**
     * GET /dashboard/vikon-updates
     * Show update management page
     */
    public function index(Request $request): \Inertia\Response
    {
        $isAuthenticated = $this->hasValidVikonSession();

        return inertia()->render('Dashboard/VikonUpdates/Index', [
            'is_authenticated' => $isAuthenticated,
            'current_version' => $this->getCurrentVersion(),
            'modules' => config('vikon.modules'),
            'vikon_auth_domain' => config('vikon.api_domain'),
            'vikon_client_id' => config('vikon.client_id'),
        ]);
    }

    /**
     * POST /dashboard/vikon-updates/authenticate
     * Exchange OAuth code for tokens
     */
    public function authenticate(AuthenticateVikonRequest $request): JsonResponse
    {
        try {
            $tokens = $this->authenticateAction->run(
                $request->validated('code'),
                $request->validated('redirect_uri')
            );

            // Store tokens in secure session (HttpOnly cookie)
            Session::put('vikon_access_token', $tokens['access_token']);
            Session::put('vikon_refresh_token', $tokens['refresh_token']);

            return response()->json([
                'success' => true,
                'message' => 'Авторизация успешна',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Vikon authentication failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка авторизации. Пожалуйста, попробуйте снова.',
            ], 422);
        }
    }

    /**
     * POST /dashboard/vikon-updates/refresh-token
     * Refresh access token
     */
    public function refreshToken(RefreshVikonTokenRequest $request): JsonResponse
    {
        try {
            $tokens = $this->refreshTokenTask->run(
                $request->validated('refresh_token')
            );

            Session::put('vikon_access_token', $tokens['access_token']);
            Session::put('vikon_refresh_token', $tokens['refresh_token']);

            return response()->json([
                'success' => true,
                'message' => 'Токен обновлён',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Vikon token refresh failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка обновления токена. Пожалуйста, выполните повторную авторизацию.',
            ], 422);
        }
    }

    /**
     * POST /dashboard/vikon-updates/check-access
     * Verify user has update permissions
     */
    public function checkAccess(Request $request): JsonResponse
    {
        $accessToken = Session::get('vikon_access_token');

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Требуется авторизация',
                'requires_auth' => true,
            ], 401);
        }

        $result = $this->checkAccessAction->run($accessToken);

        return response()->json([
            'success' => $result['has_access'],
            'has_access' => $result['has_access'],
            'error' => $result['error'],
            'permissions' => $result['permissions'],
        ]);
    }

    /**
     * POST /dashboard/vikon-updates/check-version
     * Check for available updates
     */
    public function checkVersion(Request $request): JsonResponse
    {
        $accessToken = Session::get('vikon_access_token');

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Требуется авторизация',
                'requires_auth' => true,
            ], 401);
        }

        $result = $this->checkVersionAction->run($accessToken);

        return response()->json($result);
    }

    /**
     * POST /dashboard/vikon-updates/download-update
     * Download and install module update
     */
    public function downloadUpdate(DownloadModuleUpdateRequest $request): JsonResponse
    {
        $accessToken = Session::get('vikon_access_token');

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Требуется авторизация',
                'requires_auth' => true,
            ], 401);
        }

        try {
            $message = $this->downloadUpdateAction->run(
                $request->validated('module_id'),
                $accessToken
            );

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Vikon module update failed', [
                'module_id' => $request->validated('module_id'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при обновлении модуля. Обратитесь к администратору.',
            ], 500);
        }
    }

    /**
     * POST /dashboard/vikon-updates/sync-files
     * Initialize file sync for module
     */
    public function syncFiles(Request $request): JsonResponse
    {
        $request->validate([
            'module_id' => ['required', 'integer', 'in:1,2,6'],
        ]);

        $accessToken = Session::get('vikon_access_token');

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Требуется авторизация',
                'requires_auth' => true,
            ], 401);
        }

        try {
            $result = $this->syncFilesAction->run(
                $request->input('module_id'),
                $accessToken
            );

            return response()->json([
                'success' => true,
                'directories' => $result['directories'],
                'files_to_sync' => $result['files_to_sync'],
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Vikon file sync failed', [
                'module_id' => $request->input('module_id'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при синхронизации файлов.',
            ], 500);
        }
    }

    /**
     * GET /dashboard/vikon-updates/check-entry
     * Verify current URL is valid entry point
     */
    public function checkEntry(Request $request): JsonResponse
    {
        $entryPoint = $request->input('entry', url()->current());

        try {
            $isValid = $this->checkEntryPointTask->run($entryPoint);

            return response()->json([
                'success' => $isValid,
                'entry_point' => $entryPoint,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Vikon entry point check failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Не удалось проверить точку входа.',
            ], 500);
        }
    }

    /**
     * POST /dashboard/vikon-updates/logout
     * Clear Vikon session
     */
    public function logout(Request $request): JsonResponse
    {
        Session::forget(['vikon_access_token', 'vikon_refresh_token']);

        return response()->json([
            'success' => true,
            'message' => 'Сессия завершена',
        ]);
    }

    /**
     * Check if user has valid Vikon session
     */
    private function hasValidVikonSession(): bool
    {
        $accessToken = Session::get('vikon_access_token');

        if (!$accessToken) {
            return false;
        }

        return $this->validateTokenTask->run($accessToken);
    }
}
