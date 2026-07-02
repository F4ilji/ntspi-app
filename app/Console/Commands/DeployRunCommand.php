<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Process;

class DeployRunCommand extends Command
{
    protected $signature = 'deploy:run';
    protected $description = 'Execute deployment process';

    private string $statusFile = '/tmp/deploy-status.json';
    private string $lockFile = '/tmp/deploy.lock';
    private int $totalSteps = 13;

    public function handle(): int
    {
        if (File::exists($this->lockFile)) {
            $this->error('Deploy already in progress');
            return 1;
        }

        File::put($this->lockFile, (string) getpid());
        $this->initStatus();

        try {
            $this->executeDeploy();
            $this->updateStatus([
                'running' => false,
                'success' => true,
            ]);
            return 0;
        } catch (\Exception $e) {
            $this->handleError($e);
            return 1;
        } finally {
            File::delete($this->lockFile);
        }
    }

    private function initStatus(): void
    {
        $this->writeStatus([
            'running' => true,
            'step' => 0,
            'total_steps' => $this->totalSteps,
            'current_step' => '',
            'started_at' => now()->toIso8601String(),
            'logs' => [],
            'error' => null,
        ]);
    }

    private function writeStatus(array $data): void
    {
        File::put($this->statusFile, json_encode($data));
    }

    private function updateStatus(array $updates): void
    {
        $status = json_decode(File::get($this->statusFile), true);
        $status = array_merge($status, $updates);
        $this->writeStatus($status);
    }

    private function addLog(string $message): void
    {
        $status = json_decode(File::get($this->statusFile), true);
        $status['logs'][] = '[' . now()->format('H:i:s') . '] ' . $message;
        $this->writeStatus($status);
        $this->line($message);
    }

    private function executeStep(int $step, string $name, callable $callback): void
    {
        $this->updateStatus([
            'step' => $step,
            'current_step' => $name,
        ]);
        $this->addLog("Step {$step}/{$this->totalSteps}: {$name}");

        $callback();

        $this->addLog("✅ {$name} completed");
    }

    private function executeDeploy(): void
    {
        $this->executeStep(1, 'Maintenance mode', function () {
            $this->execCommand('php artisan down');
        });

        $this->executeStep(2, 'Git pull', function () {
            $this->execCommand('git config --global --add safe.directory /var/www');
            $this->execCommand('git reset --hard');
            $this->execCommand('git pull origin master');
        });

        $this->executeStep(3, 'Composer install', function () {
            $this->execCommand('composer install --no-dev --no-interaction --prefer-dist --no-cache');
        });

        $this->executeStep(4, 'Database backup', function () {
            $backupDir = storage_path('app/backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql.gz';
            $filepath = $backupDir . '/' . $filename;
            $dbHost = env('DB_HOST', 'db');
            $dbName = env('DB_DATABASE', 'ntspi_db');
            $dbUser = env('DB_USERNAME', 'admin');
            $dbPass = env('DB_PASSWORD', 'secret');
            $this->execCommand(
                "mysqldump -h {$dbHost} -u {$dbUser} -p{$dbPass} {$dbName} | gzip > {$filepath}"
            );
            $this->addLog("Backup saved: {$filepath}");
        });

        $this->executeStep(5, 'Migrate', function () {
            $this->execCommand('php artisan migrate --force');
        });

        $this->executeStep(6, 'NPM install', function () {
            $this->execCommand('npm install --legacy-peer-deps');
        });

        $this->executeStep(7, 'NPM build', function () {
            $this->execCommand('npm run build');
        });

        $this->executeStep(8, 'Cache clear', function () {
            $this->execCommand('php artisan cache:clear');
            $this->execCommand('php artisan config:clear');
            $this->execCommand('php artisan route:clear');
            $this->execCommand('php artisan view:clear');
            $this->execCommand('php artisan filament:clear-cached-components');
            $this->execCommand('php artisan filament:optimize-clear');
        });

        $this->executeStep(9, 'Cache warm', function () {
            $this->execCommand('php artisan routes:register');
            $this->execCommand('php artisan route:cache');
            $this->execCommand('php artisan view:cache');
            $this->execCommand('php artisan icons:cache');
            $this->execCommand('php artisan filament:cache-components');
            $this->execCommand('php artisan filament:optimize');
        });

        $this->executeStep(10, 'Fix permissions', function () {
            $this->execCommand('chown -R www-data:www-data storage bootstrap/cache');
            $this->execCommand('chmod -R 775 storage bootstrap/cache');
        });

        $this->executeStep(11, 'Restart processes', function () {
            $this->execCommand('supervisorctl restart inertia-ssr');
            $this->execCommand('supervisorctl restart cron');
        });

        $this->executeStep(12, 'Restart queue workers', function () {
            $this->execCommand('php artisan queue:restart');
        });

        $this->executeStep(13, 'Maintenance off', function () {
            $this->execCommand('php artisan up');
        });
    }

    private function execCommand(string $command): void
    {
        $process = Process::run($command);

        if ($process->exitCode() !== 0) {
            throw new \RuntimeException(
                "Command failed: {$command}\n" . $process->errorOutput()
            );
        }
    }

    private function handleError(\Exception $e): void
    {
        $this->addLog("❌ Error: " . $e->getMessage());

        $this->updateStatus([
            'running' => false,
            'success' => false,
            'error' => $e->getMessage(),
        ]);

        $this->addLog('Attempting rollback...');

        try {
            $this->execCommand('git checkout .');
            $this->execCommand('composer install --no-dev --prefer-dist');
            $this->execCommand('php artisan up');
            $this->addLog('Rollback completed');
        } catch (\Exception $rollbackException) {
            $this->addLog("❌ Rollback failed: " . $rollbackException->getMessage());
        }
    }
}
