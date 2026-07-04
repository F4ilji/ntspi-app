<?php

namespace App\Containers\VikonIntegration\Actions;

use App\Containers\VikonIntegration\Tasks\HttpTask;
use App\Containers\VikonIntegration\Tasks\ValidateTokenTask;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CheckAccessAction
{
    public function __construct(
        private readonly ValidateTokenTask $validateToken,
        private readonly HttpTask $http,
        private readonly string $publicPath,
    ) {}

    public function run(string $accessToken): array
    {
        if (!$this->validateToken->run($accessToken)) {
            return ['has_access' => false, 'error' => 'Токен недействителен. Выполните повторную авторизацию.'];
        }

        try {
            $response = $this->http->getWithToken('pull_updates/assist/getModulesTreeAsync', $accessToken);
            $body = $response->json();

            if (!isset($body['tree_access'])) {
                return [
                    'has_access' => false,
                    'error' => 'Нет прав на обновление. Обратитесь к администратору VIKON.',
                ];
            }

            $nonWritable = $this->findNonWritable($this->publicPath);
            if (!empty($nonWritable)) {
                return [
                    'has_access' => false,
                    'error' => 'Нет прав на запись: ' . implode(', ', array_slice($nonWritable, 0, 3)),
                ];
            }

            return [
                'has_access' => true,
                'error' => null,
                'modules_tree' => $body['tree_access'],
            ];
        } catch (\Throwable $e) {
            Log::error('Vikon access check failed', ['error' => $e->getMessage()]);
            return [
                'has_access' => false,
                'error' => 'Не удалось проверить права: ' . $e->getMessage(),
            ];
        }
    }

    private function findNonWritable(string $path, int $depth = 0): array
    {
        if ($depth > 3 || !is_dir($path)) return [];

        $relative = str_replace(base_path() . '/', '', $path);

        $excluded = ['vendor', 'node_modules', 'storage', '.git', 'bootstrap/cache'];
        foreach ($excluded as $ex) {
            if (str_starts_with($relative, $ex)) return [];
        }

        if (!is_writable($path)) {
            return [$relative];
        }

        $result = [];
        foreach (File::directories($path) as $dir) {
            $result = array_merge($result, $this->findNonWritable($dir, $depth + 1));
        }
        return $result;
    }
}
