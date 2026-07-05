<?php

namespace App\Containers\VikonIntegration\Actions;

use App\Containers\VikonIntegration\Tasks\HttpTask;
use App\Containers\VikonIntegration\Tasks\FilesystemTask;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class UpdateCoreAction
{
    public function __construct(
        private readonly HttpTask $http,
        private readonly FilesystemTask $fs,
        private readonly array $modulesConfig,
        private readonly string $storagePath,
        private readonly string $basePath,
    ) {}

    public function run(int $moduleId, string $accessToken): string
    {
        $config = $this->modulesConfig[$moduleId] ?? throw new \RuntimeException('Неизвестный модуль');
        $modulePath = $this->basePath . '/' . $config['path'];
        $tempPath = $this->storagePath . '/temp/' . $config['path'];

        Log::info('Vikon: starting full update', ['module' => $moduleId]);

        $this->downloadCore($moduleId, $modulePath, $tempPath, $accessToken);
        $this->syncFromFM($moduleId, $modulePath, $accessToken);
        $this->cleanupPostSync($modulePath);

        File::put($modulePath . '/.vikon', date('Y-m-d H:i:s'));

        // Update version file from API
        $this->updateVersionFile($accessToken);

        Log::info('Vikon: full update complete', ['module' => $config['name']]);
        return 'Модуль "' . $config['name'] . '" полностью обновлён.';
    }

    private function downloadCore(int $moduleId, string $modulePath, string $tempPath, string $accessToken): void
    {
        $moduleConfig = $this->modulesConfig[$moduleId] ?? [];
        if (!empty($moduleConfig['init_only'])) {
            $this->initModuleFromApi($moduleId, $modulePath, $accessToken);
            return;
        }

        File::makeDirectory($tempPath, 0755, true, true);

        $zipFile = $tempPath . '/module.zip';
        $this->http->downloadToFile(
            'pull_updates/generateEmptyModuleCore/' . $moduleId,
            $accessToken,
            $zipFile
        );

        Log::info('Vikon: ZIP downloaded', ['size' => filesize($zipFile), 'module' => $moduleId]);

        $this->extractZip($zipFile, $tempPath);

        // Log what was extracted
        $extracted = array_map(fn($f) => basename($f), File::allFiles($tempPath));
        Log::info('Vikon: extracted files', ['count' => count($extracted), 'sample' => array_slice($extracted, 0, 10)]);

        $blocked = $this->fs->validateFileTypes($tempPath);
        if (!empty($blocked)) {
            throw new \RuntimeException('Запрещённые файлы: ' . implode(', ', $blocked));
        }

        $extractedDirs = File::directories($tempPath);
        $syncSource = $extractedDirs[0] ?? $tempPath;
        File::makeDirectory($modulePath, 0755, true, true);
        $this->copyFiles($syncSource, $modulePath);

        Log::info('Vikon: core downloaded', ['module' => $moduleId]);
    }

    private function syncFromFM(int $moduleId, string $modulePath, string $accessToken): void
    {
        $filesDir = $modulePath . '/files';
        File::makeDirectory($modulePath, 0755, true, true);
        File::makeDirectory($filesDir, 0755, true, true);

        $dirIds = $this->getUsedDirNames($moduleId, $accessToken);
        Log::info('Vikon FM: dir identifiers', ['count' => count($dirIds), 'dirs' => $dirIds]);

        $synced = 0;

        $synced += $this->syncRootDir($moduleId, $filesDir, $accessToken);

        foreach ($dirIds as $dirId) {
            $synced += $this->syncSubDir($dirId, $moduleId, $filesDir, $accessToken);
        }

        $newSynced = $this->syncNewFiles($moduleId, $accessToken, $filesDir);

        Log::info('Vikon FM: sync done', ['synced' => $synced, 'new' => $newSynced]);
    }

    private function syncRootDir(int $moduleId, string $filesDir, string $accessToken): int
    {
        $response = $this->http->getWithToken(
            "sync/getFileNamesFromRootDirectoryByModule?moduleId={$moduleId}",
            $accessToken,
            'filemanager'
        );

        $files = $response->json()['files'] ?? [];
        if (empty($files)) return 0;

        $existingFiles = [];
        if (File::isDirectory($filesDir)) {
            foreach (File::files($filesDir) as $f) {
                $existingFiles[$f->getFilename()] = $f->getSize();
            }
        }

        $serverCount = count($files);
        $localCount = count($existingFiles);
        $toDownload = 0;

        $synced = 0;
        foreach ($files as $file) {
            $name = $file['n'] ?? null;
            $identity = $file['i'] ?? null;
            if (!$name || !$identity) continue;

            if (isset($existingFiles[$name]) && $existingFiles[$name] > 0) {
                continue;
            }

            $toDownload++;
            try {
                $content = $this->http->downloadWithToken(
                    "sync/downloadFileBinaryForSync?identity={$identity}&moduleId={$moduleId}",
                    $accessToken,
                    'filemanager'
                );
                file_put_contents($filesDir . '/' . $name, $content);
                $synced++;
            } catch (\Throwable $e) {
                Log::warning('Vikon FM: root download failed', ['identity' => $identity, 'error' => $e->getMessage()]);
            }
        }

        Log::info('Vikon FM: root dir stats', [
            'server' => $serverCount,
            'local' => $localCount,
            'to_download' => $toDownload,
            'synced' => $synced,
        ]);

        return $synced;
    }

    private function syncSubDir(string $dirId, int $moduleId, string $filesDir, string $accessToken): int
    {
        $response = $this->http->getWithToken(
            "sync/getFileNamesFromSubDirectoryByModule?dir={$dirId}&moduleId={$moduleId}",
            $accessToken,
            'filemanager'
        );

        $files = $response->json()['files'] ?? [];
        if (empty($files)) return 0;

        $targetDir = $filesDir . '/' . $dirId;
        File::makeDirectory($targetDir, 0755, true, true);

        $existingFiles = [];
        foreach (File::files($targetDir) as $f) {
            $existingFiles[$f->getFilename()] = $f->getSize();
        }

        $synced = 0;
        foreach ($files as $file) {
            $name = $file['n'] ?? null;
            $identity = $file['i'] ?? null;
            if (!$name || !$identity) continue;

            if (isset($existingFiles[$name]) && $existingFiles[$name] > 0) {
                continue;
            }

            try {
                $content = $this->http->downloadWithToken(
                    "sync/downloadFileBinaryForSync?identity={$identity}&moduleId={$moduleId}",
                    $accessToken,
                    'filemanager'
                );
                file_put_contents($targetDir . '/' . $name, $content);
                $synced++;
            } catch (\Throwable $e) {
                Log::warning('Vikon FM: sub download failed', ['identity' => $identity, 'error' => $e->getMessage()]);
            }
        }
        return $synced;
    }

    private function syncNewFiles(int $moduleId, string $accessToken, string $filesDir): int
    {
        $synced = 0;
        while (true) {
            $response = $this->http->getWithToken(
                "sync/getNewFileInfoByModule?moduleId={$moduleId}",
                $accessToken,
                'filemanager'
            );
            $body = $response->json();

            if (empty($body['file_name']) && empty($body['identity'])) break;

            $identity = $body['identity'];
            $filename = $body['file_name'];
            $directory = $body['dir_name'] ?? null;

            $targetDir = $directory ? $filesDir . '/' . $directory : $filesDir;

            try {
                $content = $this->http->downloadWithToken(
                    "sync/downloadFileBinary?identity={$identity}",
                    $accessToken,
                    'filemanager'
                );

                File::makeDirectory($targetDir, 0755, true, true);
                file_put_contents($targetDir . '/' . $filename, $content);

                $this->http->getWithToken(
                    "sync/markNewFileAsLoaded?identity={$identity}&moduleId={$moduleId}",
                    $accessToken,
                    'filemanager'
                );

                $synced++;
            } catch (\Throwable $e) {
                Log::warning('Vikon FM: new file failed', ['identity' => $identity, 'error' => $e->getMessage()]);
            }
        }
        return $synced;
    }

    private function getUsedDirNames(int $moduleId, string $accessToken): array
    {
        $response = $this->http->getWithToken(
            "sync/getUsedDirNamesByModule?moduleId={$moduleId}",
            $accessToken,
            'filemanager'
        );
        return $response->json()['directories'] ?? [];
    }

    private function initModuleFromApi(int $moduleId, string $modulePath, string $accessToken): void
    {
        $response = $this->http->getWithToken("pull_updates/generateEmptyModuleCore/{$moduleId}", $accessToken);
        $body = $response->json();

        if (!isset($body['success']) || $body['success'] !== true) {
            throw new \RuntimeException("Module init failed for module {$moduleId}: " . ($body['message'] ?? 'Unknown'));
        }

        File::makeDirectory($modulePath, 0755, true, true);
        File::makeDirectory($modulePath . '/files', 0755, true, true);
    }

    private function extractZip(string $zipPath, string $destination): void
    {
        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException('Не удалось открыть ZIP');
        }

        $realDest = realpath($destination);
        $blocked = ['php', 'phtml', 'php5', 'php7', 'php8', 'phar'];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (str_contains($name, '..')) { $zip->close(); throw new \RuntimeException("Zip Slip: {$name}"); }
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (in_array($ext, $blocked, true)) { $zip->close(); throw new \RuntimeException("Blocked: {$name}"); }
            $full = realpath($realDest . '/' . $name);
            if ($full !== false && !str_starts_with($full, $realDest)) { $zip->close(); throw new \RuntimeException("Escape: {$name}"); }
        }

        $zip->extractTo($destination);
        $zip->close();
    }

    private function copyFiles(string $source, string $target): void
    {
        File::makeDirectory($target, 0755, true, true);

        foreach (File::files($source) as $file) {
            rename($file->getPathname(), $target . '/' . $file->getFilename());
        }

        foreach (File::directories($source) as $dir) {
            $name = basename($dir);
            $targetPath = $target . '/' . $name;
            if (!File::isDirectory($targetPath)) {
                rename($dir, $targetPath);
            } else {
                $this->copyFiles($dir, $targetPath);
            }
        }
    }

    private function cleanupPostSync(string $modulePath): void
    {
        if (!File::isDirectory($modulePath)) {
            return;
        }

        $removed = 0;

        // Remove _old and _new directories/files left after successful atomic swap
        foreach (File::directories($modulePath) as $dir) {
            $name = basename($dir);
            if (str_ends_with($name, '_old') || str_ends_with($name, '_new')) {
                File::deleteDirectory($dir);
                $removed++;
            }
        }

        foreach (File::files($modulePath) as $file) {
            $name = $file->getFilename();
            if (str_ends_with($name, '_old') || str_ends_with($name, '_new')) {
                File::delete($file->getPathname());
                $removed++;
            }
        }

        if ($removed > 0) {
            Log::info('Vikon: cleaned up post-sync', ['removed' => $removed]);
        }
    }

    private function updateVersionFile(string $accessToken): void
    {
        try {
            $response = $this->http->getWithToken('pull_updates/assist/getUpdateVersionJson', $accessToken);
            $body = $response->json();
            $latestVersion = $body['version'] ?? null;

            if ($latestVersion) {
                $versionFile = config('vikon.current_version_file');
                if ($versionFile) {
                    file_put_contents($versionFile, $latestVersion);
                    Log::info('Vikon: version updated', ['version' => $latestVersion]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Vikon: failed to update version file', ['error' => $e->getMessage()]);
        }
    }
}
