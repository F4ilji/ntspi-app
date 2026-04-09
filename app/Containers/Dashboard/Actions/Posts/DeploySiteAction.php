<?php

namespace App\Containers\Dashboard\Actions\Posts;

use Illuminate\Support\Facades\Log;

class DeploySiteAction
{
    private string $deployScript = '/var/www/_deploy/deploy.sh';
    private string $logFile = '/var/www/_deploy/deploy.log';

    /**
     * Запускает deploy.sh напрямую
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public function run(): array
    {
        try {
            if (!file_exists($this->deployScript)) {
                return [
                    'success' => false,
                    'message' => 'Скрипт деплоя не найден на сервере',
                ];
            }

            // Проверяем, не запущен ли уже деплой
            if ($this->isDeployRunning()) {
                return [
                    'success' => false,
                    'message' => 'Деплой уже запущен! Подождите завершения текущего процесса.',
                ];
            }

            // Очищаем старый лог
            if (file_exists($this->logFile)) {
                unlink($this->logFile);
            }

            // Запускаем deploy.sh в фоне
            $command = sprintf(
                'nohup bash %s > %s 2>&1 &',
                escapeshellarg($this->deployScript),
                escapeshellarg($this->logFile)
            );

            shell_exec($command);

            // Даём процессу время на запуск
            usleep(500000); // 0.5 сек

            if (!$this->isDeployRunning()) {
                // Процесс не запустился — читаем лог
                $error = file_exists($this->logFile) ? file_get_contents($this->logFile) : 'Нет лога';
                Log::error('Deploy script failed to start', ['log' => $error]);

                return [
                    'success' => false,
                    'message' => 'Скрипт деплоя не запустился. Проверьте лог.',
                ];
            }

            Log::info('Deploy triggered', ['user_id' => auth()->id()]);

            return [
                'success' => true,
                'message' => 'Деплой запущен! Процесс обновления сайта начнётся в течение нескольких секунд.',
            ];
        } catch (\Exception $e) {
            Log::error('Deploy trigger failed', ['exception' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Ошибка при запуске деплоя: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Проверяет статус деплоя
     */
    public function getStatus(): array
    {
        if (!file_exists($this->logFile)) {
            return ['status' => 'idle', 'message' => 'Деплой не запущен'];
        }

        $log = file_get_contents($this->logFile);

        // Проверяем, запущен ли процесс
        if ($this->isDeployRunning()) {
            return [
                'status' => 'running',
                'message' => 'Деплой выполняется...',
                'log' => $log,
            ];
        }

        // Процесс завершил — проверяем результат
        $lastLines = array_slice(explode("\n", trim($log)), -5);
        $lastOutput = implode("\n", $lastLines);

        $isSuccess = stripos($lastOutput, 'успешн') !== false
            || stripos($lastOutput, 'success') !== false
            || stripos($lastOutput, '✅') !== false;

        $isFailed = stripos($lastOutput, 'ошибк') !== false
            || stripos($lastOutput, 'error') !== false
            || stripos($lastOutput, '❌') !== false;

        if ($isSuccess) {
            return [
                'status' => 'completed',
                'message' => 'Деплой завершён успешно!',
                'log' => $log,
            ];
        }

        if ($isFailed) {
            return [
                'status' => 'failed',
                'message' => 'Деплой завершён с ошибкой',
                'log' => $log,
            ];
        }

        return [
            'status' => 'unknown',
            'message' => 'Статус неизвестен',
            'log' => $log,
        ];
    }

    /**
     * Очищает статус деплоя
     */
    public function clearStatus(): void
    {
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
    }

    /**
     * Проверяет, запущен ли процесс деплоя
     */
    private function isDeployRunning(): bool
    {
        $output = shell_exec('pgrep -f "deploy\\.sh"');

        return !empty(trim($output ?? ''));
    }
}
