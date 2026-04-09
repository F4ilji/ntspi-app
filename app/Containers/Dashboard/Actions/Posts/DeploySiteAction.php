<?php

namespace App\Containers\Dashboard\Actions\Posts;

use Illuminate\Support\Facades\Log;

class DeploySiteAction
{
    private string $deployScript = '/var/www/_deploy/deploy.sh';
    private string $logFile = '/var/www/_deploy/deploy.log';
    private string $pidFile = '/var/www/_deploy/deploy.pid';

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

            // Запускаем deploy.sh в фоне через proc_open
            $command = sprintf(
                'nohup bash %s > %s 2>&1 & echo $!',
                escapeshellarg($this->deployScript),
                escapeshellarg($this->logFile)
            );

            $descriptors = [
                1 => ['pipe', 'w'],  // stdout
                2 => ['pipe', 'w'],  // stderr
            ];

            $process = proc_open($command, $descriptors, $pipes);

            if (!is_resource($process)) {
                Log::error('Deploy script failed to start — proc_open unavailable');
                return [
                    'success' => false,
                    'message' => 'Ошибка при запуске скрипта деплоя (proc_open недоступен)',
                ];
            }

            // Читаем PID процесса
            $pid = trim(stream_get_contents($pipes[1]));
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);

            if (!empty($pid) && is_numeric($pid)) {
                file_put_contents($this->pidFile, $pid);
            }

            Log::info('Deploy triggered', ['user_id' => auth()->id(), 'pid' => $pid]);

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
        if (file_exists($this->pidFile)) {
            unlink($this->pidFile);
        }
    }

    /**
     * Проверяет, запущен ли процесс деплоя
     */
    private function isDeployRunning(): bool
    {
        if (!file_exists($this->pidFile)) {
            return false;
        }

        $pid = trim(file_get_contents($this->pidFile));
        if (empty($pid)) {
            return false;
        }

        // Проверяем существование процесса
        exec(sprintf('kill -0 %s 2>/dev/null', escapeshellarg($pid)), $output, $exitCode);

        return $exitCode === 0;
    }
}
