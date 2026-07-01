<?php

namespace App\Containers\Dashboard\Actions\Posts;

use App\Containers\Dashboard\Tasks\DeployTask;
use Illuminate\Support\Facades\Log;

class DeploySiteAction
{
    public function __construct(
        private readonly DeployTask $deployTask,
    ) {}

    public function run(): array
    {
        try {
            if (!$this->deployTask->scriptExists()) {
                Log::channel('deploy')->warning('[DeployAction] Script not found');
                return [
                    'success' => false,
                    'message' => 'Скрипт деплоя не найден на сервере',
                ];
            }

            if ($this->deployTask->isDeployRunning()) {
                Log::channel('deploy')->warning('[DeployAction] Deploy already running', [
                    'user_id' => auth()->id(),
                ]);
                return [
                    'success' => false,
                    'message' => 'Деплой уже запущен! Подождите завершения текущего процесса.',
                ];
            }

            $started = $this->deployTask->startDeploy();

            if (!$started) {
                Log::channel('deploy')->error('[DeployAction] Failed to start deploy script', [
                    'user_id' => auth()->id(),
                ]);
                return [
                    'success' => false,
                    'message' => 'Скрипт деплоя не запустился. Проверьте лог.',
                ];
            }

            Log::channel('deploy')->info('[DeployAction] Deploy triggered successfully', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()?->email,
            ]);

            return [
                'success' => true,
                'message' => 'Деплой запущен! Процесс обновления сайта начнётся в течение нескольких секунд.',
            ];
        } catch (\Exception $e) {
            Log::channel('deploy')->error('[DeployAction] Exception', [
                'user_id' => auth()->id(),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Ошибка при запуске деплоя: ' . $e->getMessage(),
            ];
        }
    }

    public function getStatus(): array
    {
        return $this->deployTask->getDeployStatus();
    }

    public function getLog(int $lines = 50): array
    {
        return [
            'log' => $this->deployTask->getLogTail($lines),
            'full_log' => $this->deployTask->getLog(),
        ];
    }

    public function getHistory(): array
    {
        return [
            'history' => $this->deployTask->getHistory(),
        ];
    }

    public function clearStatus(): void
    {
        Log::channel('deploy')->info('[DeployAction] Log cleared', [
            'user_id' => auth()->id(),
        ]);
        $this->deployTask->clearLog();
    }
}
