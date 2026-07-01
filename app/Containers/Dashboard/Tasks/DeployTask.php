<?php

namespace App\Containers\Dashboard\Tasks;

use Illuminate\Support\Facades\Log;

class DeployTask
{
    private string $deployScript = '/var/www/_deploy/deploy.sh';
    private string $logFile = '/var/www/_deploy/deploy.log';
    private string $historyDir = '/var/www/_deploy/history';

    public function scriptExists(): bool
    {
        $exists = file_exists($this->deployScript);
        $this->phpLog('check_script', $exists ? 'found' : 'not_found');

        return $exists;
    }

    public function isDeployRunning(): bool
    {
        $output = shell_exec('pgrep -f "deploy\\.sh"');
        $running = !empty(trim($output ?? ''));

        return $running;
    }

    public function startDeploy(): bool
    {
        if ($this->isDeployRunning()) {
            $this->phpLog('start', 'already_running');
            return false;
        }

        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }

        $this->phpLog('start', 'launching deploy.sh');

        $command = sprintf(
            'bash %s > /dev/null 2>&1 &',
            escapeshellarg($this->deployScript)
        );

        shell_exec($command);
        usleep(500000);

        $started = $this->isDeployRunning();

        $this->phpLog('start', $started ? 'process_started' : 'process_failed_to_start');

        return $started;
    }

    public function getDeployStatus(): array
    {
        if (!$this->isDeployRunning()) {
            if (!file_exists($this->logFile)) {
                $this->phpLog('status', 'idle');
                return ['status' => 'idle', 'message' => 'Деплой не запущен'];
            }

            $log = $this->getLog();
            $lastLines = array_slice(explode("\n", trim($log)), -5);
            $lastOutput = implode("\n", $lastLines);

            $isSuccess = stripos($lastOutput, '✅') !== false
                || stripos($lastOutput, 'completed successfully') !== false;

            $isFailed = stripos($lastOutput, '❌') !== false
                || stripos($lastOutput, 'ERROR') !== false;

            $status = $isSuccess ? 'completed' : ($isFailed ? 'failed' : 'unknown');
            $this->phpLog('status', $status);

            return [
                'status' => $status,
                'message' => $isSuccess ? 'Деплой завершён успешно!' : 'Деплой завершён',
                'log' => $log,
            ];
        }

        $this->phpLog('status', 'running');

        return [
            'status' => 'running',
            'message' => 'Деплой выполняется...',
            'log' => $this->getLog(),
        ];
    }

    public function getLog(): string
    {
        if (!file_exists($this->logFile)) {
            return '';
        }
        return file_get_contents($this->logFile);
    }

    public function getLogTail(int $lines = 50): string
    {
        $log = $this->getLog();
        if (empty($log)) {
            return '';
        }
        $allLines = explode("\n", trim($log));
        return implode("\n", array_slice($allLines, -$lines));
    }

    public function clearLog(): void
    {
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }

        $this->phpLog('clear', 'deploy log cleared');
    }

    public function getHistory(): array
    {
        if (!is_dir($this->historyDir)) {
            return [];
        }

        $files = glob($this->historyDir . '/*.json');
        usort($files, fn($a, $b) => strcmp($b, $a));

        $history = [];
        foreach (array_slice($files, 0, 50) as $file) {
            $content = file_get_contents($file);
            $data = json_decode($content, true);
            if ($data) {
                $history[] = $data;
            }
        }

        return $history;
    }

    private function phpLog(string $event, string $message): void
    {
        Log::channel('deploy')->info('[Deploy] {event}: {message}', [
            'event' => $event,
            'message' => $message,
        ]);
    }
}
