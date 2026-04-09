<?php

namespace App\Containers\Dashboard\Actions\Posts;

use Illuminate\Support\Facades\Log;

class DeploySiteAction
{
    private string $lockFile = '/var/www/_deploy/deploy.lock';
    private string $logFile = '/var/www/_deploy/deploy.log';

    /**
     * Создаёт файл-триггер для запуска деплоя
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public function run(): array
    {
        try {
            // Проверяем, не запущен ли уже деплой
            if (file_exists($this->lockFile)) {
                $lockContent = file_get_contents($this->lockFile);
                $lockData = json_decode($lockContent, true);

                if ($lockData && isset($lockData['status']) && $lockData['status'] === 'running') {
                    return [
                        'success' => false,
                        'message' => 'Деплой уже запущен! Подождите завершения текущего процесса.',
                    ];
                }
            }

            // Создаём файл-триггер
            $lockData = [
                'status' => 'pending',
                'started_at' => now()->toDateTimeString(),
                'started_by' => auth()->id(),
            ];

            file_put_contents($this->lockFile, json_encode($lockData, JSON_PRETTY_PRINT));

            Log::info('Deploy triggered', ['user_id' => auth()->id()]);

            return [
                'success' => true,
                'message' => 'Деплой запущен! Процесс обновления сайта начнётся в течение 1 минуты.',
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
        if (!file_exists($this->lockFile)) {
            return ['status' => 'idle', 'message' => 'Деплой не запущен'];
        }

        $content = file_get_contents($this->lockFile);
        $data = json_decode($content, true);

        if (!$data) {
            return ['status' => 'error', 'message' => 'Ошибка чтения статуса'];
        }

        // Читаем лог если есть
        $log = '';
        if (file_exists($this->logFile)) {
            $log = file_get_contents($this->logFile);
        }

        return [
            'status' => $data['status'] ?? 'unknown',
            'started_at' => $data['started_at'] ?? null,
            'completed_at' => $data['completed_at'] ?? null,
            'message' => $data['message'] ?? '',
            'log' => $log,
        ];
    }

    /**
     * Очищает статус деплоя
     */
    public function clearStatus(): void
    {
        if (file_exists($this->lockFile)) {
            unlink($this->lockFile);
        }
    }
}
