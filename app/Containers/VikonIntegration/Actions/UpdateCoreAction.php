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

        File::put($modulePath . '/.vikon', date('Y-m-d H:i:s'));

        Log::info('Vikon: full update complete', ['module' => $config['name']]);
        return 'Модуль "' . $config['name'] . '" полностью обновлён.';
    }

    private function downloadCore(int $moduleId, string $modulePath, string $tempPath, string $accessToken): void
    {
        if ($moduleId === 2) {
            $this->initAbiturModule($modulePath, $accessToken);
            return;
        }

        $zipContent = $this->http->downloadWithToken(
            'pull_updates/generateEmptyModuleCore/' . $moduleId,
            $accessToken
        );

        if (File::exists($tempPath)) File::deleteDirectory($tempPath);
        File::makeDirectory($tempPath, 0755, true, true);

        $zipFile = $tempPath . '/module.zip';
        file_put_contents($zipFile, $zipContent);

        $this->extractZip($zipFile, $tempPath);

        $blocked = $this->fs->validateFileTypes($tempPath);
        if (!empty($blocked)) {
            throw new \RuntimeException('Запрещённые файлы: ' . implode(', ', $blocked));
        }

        File::delete($zipFile);

        $extractedDirs = File::directories($tempPath);
        $syncSource = $extractedDirs[0] ?? $tempPath;
        File::makeDirectory($modulePath, 0755, true, true);
        $this->copyFiles($syncSource, $modulePath);

        Log::info('Vikon: core downloaded', ['module' => $moduleId]);
    }

    private function syncFromFM(int $moduleId, string $modulePath, string $accessToken): void
    {
        $filesDir = $modulePath . '/files';
        File::makeDirectory($filesDir, 0755, true, true);

        $dirIds = $this->getUsedDirNames($moduleId, $accessToken);
        Log::info('Vikon FM: dir identifiers', ['count' => count($dirIds)]);

        $synced = 0;

        $synced += $this->syncDirFiles('root', $moduleId, $filesDir, $accessToken);

        foreach ($dirIds as $dirId) {
            $synced += $this->syncDirFiles($dirId, $moduleId, $filesDir, $accessToken);
        }

        $newSynced = $this->syncNewFiles($moduleId, $accessToken, $filesDir);

        Log::info('Vikon FM: sync done', ['synced' => $synced, 'new' => $newSynced]);
    }

    private function syncDirFiles(string $dirId, int $moduleId, string $filesDir, string $accessToken): int
    {
        if ($dirId === 'root') {
            $response = $this->http->getWithToken(
                "sync/getFileNamesFromRootDirectoryByModule?moduleId={$moduleId}",
                $accessToken,
                'filemanager'
            );
        } else {
            $response = $this->http->getWithToken(
                "sync/getFileNamesFromSubDirectoryByModule?dir={$dirId}&moduleId={$moduleId}",
                $accessToken,
                'filemanager'
            );
        }

        $files = $response->json()['files'] ?? [];
        if (empty($files)) return 0;

        $synced = 0;
        foreach ($files as $file) {
            $identity = $file['i'] ?? null;
            if (!$identity) continue;

            try {
                $infoResp = $this->http->getWithToken(
                    "sync/getFileByIdentityInfo?identity={$identity}",
                    $accessToken,
                    'filemanager'
                );
                $info = $infoResp->json();

                $filename = $info['file_name'] ?? $file['n'] ?? null;
                $dirName = $info['dir_name'] ?? null;

                if (!$filename) continue;

                $targetDir = $dirName ? $filesDir . '/' . $dirName : $filesDir;
                File::makeDirectory($targetDir, 0755, true, true);

                $content = $this->http->downloadWithToken(
                    "sync/downloadFileBinaryForSync?identity={$identity}&moduleId={$moduleId}",
                    $accessToken,
                    'filemanager'
                );
                file_put_contents($targetDir . '/' . $filename, $content);
                $synced++;
            } catch (\Throwable $e) {
                Log::warning('Vikon FM: download failed', ['identity' => $identity, 'error' => $e->getMessage()]);
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

    private function initAbiturModule(string $modulePath, string $accessToken): void
    {
        $response = $this->http->getWithToken('pull_updates/generateEmptyModuleCore/2', $accessToken);
        $body = $response->json();

        if (!isset($body['success']) || $body['success'] !== true) {
            throw new \RuntimeException('ABITUR init failed: ' . ($body['message'] ?? 'Unknown'));
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
}
