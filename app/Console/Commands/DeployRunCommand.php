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
    private int $totalSteps = 12;

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
            $this->runCommand('php artisan down');
        });

        $this->executeStep(2, 'Git pull', function () {
            $this->runCommand('git config --global --add safe.directory /var/www');
            $this->runCommand('git reset --hard');
            $this->runCommand('git pull origin master');
        });

        $this->executeStep(3, 'Composer install', function () {
            $this->runCommand('composer install --no-dev --no-interaction --prefer-dist --no-cache');
        });

        $this->executeStep(4, 'Migrate', function () {
            $this->runCommand('php artisan migrate --force');
        });

        $this->executeStep(5, 'NPM install', function () {
            $this->runCommand('npm install --legacy-peer-deps');
        });

        $this->executeStep(6, 'NPM build', function () {
            $this->runCommand('npm run build');
        });

        $this->executeStep(7, 'Cache clear', function () {
            $this->runCommand('php artisan cache:clear');
            $this->runCommand('php artisan config:clear');
            $this->runCommand('php artisan route:clear');
            $this->runCommand('php artisan view:clear');
            $this->runCommand('php artisan filament:clear-cached-components');
            $this->runCommand('php artisan filament:optimize-clear');
        });

        $this->executeStep(8, 'Cache warm', function () {
            $this->runCommand('php artisan routes:register');
            $this->runCommand('php artisan route:cache');
            $this->runCommand('php artisan view:cache');
            $this->runCommand('php artisan icons:cache');
            $this->runCommand('php artisan filament:cache-components');
            $this->runCommand('php artisan filament:optimize');
        });

        $this->executeStep(9, 'Fix permissions', function () {
            $this->runCommand('chown -R www-data:www-data storage bootstrap/cache');
            $this->runCommand('chmod -R 775 storage bootstrap/cache');
        });

        $this->executeStep(10, 'Restart containers', function () {
            $this->runCommand('docker compose down');
            $this->runCommand('docker compose up -d --remove-orphans');
        });

        $this->executeStep(11, 'Wait 10 seconds', function () {
            sleep(10);
        });

        $this->executeStep(12, 'Maintenance off', function () {
            $this->runCommand('php artisan up');
        });
    }

    private function runCommand(string $command): void
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
            $this->runCommand('git checkout .');
            $this->runCommand('composer install --no-dev --prefer-dist');
            $this->runCommand('php artisan up');
            $this->addLog('Rollback completed');
        } catch (\Exception $rollbackException) {
            $this->addLog("❌ Rollback failed: " . $rollbackException->getMessage());
        }
    }
}
