<?php

namespace App\Containers\VikonIntegration\Actions\Updates;

use App\Containers\VikonIntegration\Tasks\CallVikonApiTask;
use App\Containers\VikonIntegration\Tasks\ValidateVikonTokenTask;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * Action: Check if user has access to Vikon updates
 *
 * Verifies access_token and checks permissions for update operations.
 * Replaces old vikon_core/check_filesystem.php authorization check.
 */
class CheckVikonAccessAction
{
    public function __construct(
        private readonly ValidateVikonTokenTask $validateTokenTask,
        private readonly CallVikonApiTask $callVikonApiTask,
        private readonly string $publicPath,
    ) {}

    /**
     * Check if user has valid access and filesystem is writable
     *
     * @param string $accessToken User's access token
     * @return array{has_access: bool, error: ?string, permissions: array, writable_paths: array}
     */
    public function run(string $accessToken): array
    {
        // Step 1: Validate token
        $isValid = $this->validateTokenTask->run($accessToken);

        if (!$isValid) {
            return [
                'has_access' => false,
                'error' => 'Токен доступа недействителен или истёк. Пожалуйста, выполните повторную авторизацию.',
                'permissions' => [],
                'writable_paths' => [],
            ];
        }

        // Step 2: Check update permissions
        try {
            $response = $this->callVikonApiTask->getWithToken(
                'pull_updates/checkAccessJson',
                $accessToken
            );

            $body = $response->json();

            if (!isset($body['success']) || !$body['success']) {
                return [
                    'has_access' => false,
                    'error' => 'Нет прав на обновление. Обратитесь к администратору VIKON.',
                    'permissions' => [],
                    'writable_paths' => [],
                ];
            }

            // Extract permissions from response
            $permissions = $body['additional_access_flags'] ?? [];

            // Step 3: Check filesystem writability (recursive, like old check_filesystem.php)
            $writablePaths = $this->checkWritablePaths();
            $nonWritablePaths = $this->findNonWritablePaths($this->publicPath);

            if (!empty($nonWritablePaths)) {
                Log::warning('Vikon access: non-writable paths detected', [
                    'paths' => $nonWritablePaths,
                ]);

                return [
                    'has_access' => false,
                    'error' => 'Отсутствуют права на запись в следующие директории: ' . implode(', ', array_slice($nonWritablePaths, 0, 5)) . '. Обратитесь к администратору сервера.',
                    'permissions' => $permissions,
                    'writable_paths' => $writablePaths,
                    'non_writable_paths' => $nonWritablePaths,
                ];
            }

            return [
                'has_access' => true,
                'error' => null,
                'permissions' => $permissions,
                'writable_paths' => $writablePaths,
            ];
        } catch (\Throwable $e) {
            Log::error('Vikon access check failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'has_access' => false,
                'error' => 'Не удалось проверить права доступа: ' . $e->getMessage(),
                'permissions' => [],
                'writable_paths' => [],
            ];
        }
    }

    /**
     * Check which module directories are writable
     */
    private function checkWritablePaths(): array
    {
        $modules = config('vikon.modules', []);
        $writable = [];

        foreach ($modules as $moduleId => $moduleConfig) {
            $modulePath = $this->publicPath . '/' . $moduleConfig['path'];
            $isWritable = is_writable($modulePath);
            $writable[] = [
                'module_id' => $moduleId,
                'path' => $moduleConfig['path'],
                'writable' => $isWritable,
            ];
        }

        return $writable;
    }

    /**
     * Recursively find non-writable paths (like old isWritableRecrusive)
     *
     * Limits depth to 3 levels to avoid performance issues on large directories.
     */
    private function findNonWritablePaths(string $path, int $depth = 0): array
    {
        $nonWritable = [];

        if ($depth > 3) {
            return $nonWritable;
        }

        if (!is_dir($path)) {
            return $nonWritable;
        }

        if (!is_writable($path)) {
            $nonWritable[] = str_replace(base_path() . '/', '', $path);
            return $nonWritable;
        }

        $entries = File::directories($path);
        foreach ($entries as $entry) {
            $nonWritable = array_merge(
                $nonWritable,
                $this->findNonWritablePaths($entry, $depth + 1)
            );
        }

        return $nonWritable;
    }
}
