<?php

namespace App\Containers\VikonIntegration\Actions;

use App\Containers\VikonIntegration\Tasks\HttpTask;
use App\Containers\VikonIntegration\Tasks\ValidateTokenTask;
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

            $modulePaths = array_map(
                fn($m) => $this->publicPath . '/' . $m['path'],
                config('vikon.modules')
            );

            $nonWritable = [];
            foreach ($modulePaths as $mp) {
                if (is_dir($mp) && !is_writable($mp)) {
                    $nonWritable[] = str_replace(base_path() . '/', '', $mp);
                }
            }
            if (!empty($nonWritable)) {
                return [
                    'has_access' => false,
                    'error' => 'Нет прав на запись: ' . implode(', ', $nonWritable),
                ];
            }

            // Extract parts per module from the API response
            // API keys are sequential ("0","1","2"), real module ID is in $moduleData['id']
            $partsByModule = [];
            $flags = $body['tree_access']['additional_access_flags'] ?? [];
            if (isset($body['tree_access']) && is_array($body['tree_access'])) {
                foreach ($body['tree_access'] as $moduleData) {
                    if (!isset($moduleData['id']) || !isset($moduleData['parts']) || !is_array($moduleData['parts'])) {
                        continue;
                    }
                    $moduleId = (int) $moduleData['id'];

                    // Check if entire module is disabled
                    $modulePath = config("vikon.modules.{$moduleId}.path", '');
                    $moduleFlag = 'no_update_' . $modulePath;
                    $moduleDisabled = in_array($moduleFlag, $flags);

                    // Filter out parts disabled by flags
                    $parts = array_filter($moduleData['parts'], function ($p) use ($flags) {
                        $partFlag = 'no_update_' . $p['id'] . '_part';
                        return !in_array($partFlag, $flags);
                    });

                    $partsByModule[$moduleId] = [
                        'disabled' => $moduleDisabled,
                        'parts' => array_map(
                            fn($p) => ['id' => $p['id'], 'name' => $p['name'] ?? $p['id'], 'access' => $p['access'] ?? true],
                            array_values($parts)
                        ),
                    ];
                }
            }

            return [
                'has_access' => true,
                'error' => null,
                'modules_tree' => $body['tree_access'],
                'parts' => $partsByModule,
            ];
        } catch (\Throwable $e) {
            Log::channel('vikon')->error('Vikon access check failed', ['error' => $e->getMessage()]);
            return [
                'has_access' => false,
                'error' => 'Не удалось проверить права: ' . $e->getMessage(),
            ];
        }
    }
}
