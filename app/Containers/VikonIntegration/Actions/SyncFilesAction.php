<?php

namespace App\Containers\VikonIntegration\Actions;

use App\Containers\VikonIntegration\Tasks\HttpTask;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SyncFilesAction
{
    public function __construct(
        private readonly HttpTask $http,
        private readonly string $publicPath,
    ) {}

    public function run(int $moduleId, string $accessToken): string
    {
        $modulePath = $this->publicPath . '/' . $this->getModuleName($moduleId);

        Log::info('Vikon FM: starting file sync', ['module' => $moduleId]);

        $dirs = $this->getUsedDirNames($moduleId, $accessToken);
        $this->cleanupRemovedDirs($modulePath, $dirs);

        $filesDir = $modulePath . '/files';
        if (!File::isDirectory($filesDir)) {
            File::makeDirectory($filesDir, 0755, true, true);
        }

        $synced = 0;

        $synced += $this->syncRootDir($moduleId, $accessToken, $filesDir, $dirs);

        foreach ($dirs as $dir) {
            if ($dir === 'files') continue;
            $synced += $this->syncSubDir($dir, $moduleId, $accessToken, $filesDir);
        }

        $synced += $this->syncNewFiles($moduleId, $accessToken, $filesDir);

        Log::info('Vikon FM: sync complete', ['module' => $moduleId, 'synced' => $synced]);
        return "Синхронизировано файлов: {$synced}";
    }

    private function getModuleName(int $moduleId): string
    {
        return match ($moduleId) {
            1 => 'sveden',
            2 => 'abitur',
            6 => 'vsoko',
            default => throw new \RuntimeException("Unknown module: {$moduleId}"),
        };
    }

    private function getUsedDirNames(int $moduleId, string $accessToken): array
    {
        $response = $this->http->getWithToken(
            "sync/getUsedDirNamesByModule?moduleId={$moduleId}",
            $accessToken,
            'filemanager'
        );

        $body = $response->json();

        if (!isset($body['directories']) || !is_array($body['directories'])) {
            return [];
        }

        return $body['directories'];
    }

    private function cleanupRemovedDirs(string $modulePath, array $knownDirs): void
    {
        $filesDir = $modulePath . '/files';
        if (!File::isDirectory($filesDir)) return;

        foreach (File::directories($filesDir) as $dir) {
            $name = basename($dir);
            if (!in_array($name, $knownDirs) && !is_link($dir)) {
                File::deleteDirectory($dir);
            }
        }
    }

    private function syncRootDir(int $moduleId, string $accessToken, string $filesDir, array $knownDirs): int
    {
        $response = $this->http->getWithToken(
            "sync/getFileNamesFromRootDirectoryByModule?moduleId={$moduleId}",
            $accessToken,
            'filemanager'
        );

        $body = $response->json();
        if (!isset($body['files']) || !is_array($body['files'])) {
            return 0;
        }

        $filesByIdentity = [];
        foreach ($body['files'] as $file) {
            $filesByIdentity[$file['n']] = $file['i'];
        }

        $existingItems = [];
        foreach (File::allFiles($filesDir) as $file) {
            $name = $file->getFilename();
            $relative = str_replace($filesDir . '/', '', $file->getPathname());
            $existingItems[$relative] = $relative;
        }

        foreach ($existingItems as $relative) {
            $name = basename($relative);
            if (!isset($filesByIdentity[$name]) && !is_dir($filesDir . '/' . $name)) {
                @unlink($filesDir . '/' . $relative);
            }
        }

        $synced = 0;
        foreach ($filesByIdentity as $fileName => $identity) {
            $filePath = $filesDir . '/' . $fileName;
            if (!File::exists($filePath) || filesize($filePath) === 0) {
                $this->downloadFile($identity, $moduleId, $filePath, $accessToken);
                $synced++;
            }
        }

        return $synced;
    }

    private function syncSubDir(string $dir, int $moduleId, string $accessToken, string $filesDir): int
    {
        $response = $this->http->getWithToken(
            "sync/getFileNamesFromSubDirectoryByModule?dir={$dir}&moduleId={$moduleId}",
            $accessToken,
            'filemanager'
        );

        $body = $response->json();
        if (!isset($body['files']) || !is_array($body['files'])) {
            return 0;
        }

        $filesByIdentity = [];
        foreach ($body['files'] as $file) {
            $filesByIdentity[$file['n']] = $file['i'];
        }

        $dirPath = $filesDir . '/' . $dir;
        if (!File::isDirectory($dirPath)) {
            File::makeDirectory($dirPath, 0775, true, true);
        }

        $existingItems = [];
        foreach (File::files($dirPath) as $file) {
            $existingItems[$file->getFilename()] = true;
        }

        foreach ($existingItems as $name => $_) {
            if (!isset($filesByIdentity[$name])) {
                @unlink($dirPath . '/' . $name);
            }
        }

        $synced = 0;
        foreach ($filesByIdentity as $fileName => $identity) {
            $filePath = $dirPath . '/' . $fileName;
            if (!File::exists($filePath) || filesize($filePath) === 0) {
                $this->downloadFile($identity, $moduleId, $filePath, $accessToken);
                $synced++;
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

            if (($body['file_name'] ?? null) === null && ($body['identity'] ?? null) === null) {
                break;
            }

            $identity = $body['identity'];
            $filename = $body['file_name'];
            $directory = $body['dir_name'] ?? null;

            $targetDir = $filesDir;
            if ($directory !== null) {
                $targetDir = $filesDir . '/' . $directory;
                if (!File::isDirectory($targetDir)) {
                    File::makeDirectory($targetDir, 0775, true, true);
                }
            }

            $filePath = $targetDir . '/' . $filename;
            $this->downloadFile($identity, $moduleId, $filePath, $accessToken);

            $this->http->getWithToken(
                "sync/markNewFileAsLoaded?identity={$identity}&moduleId={$moduleId}",
                $accessToken,
                'filemanager'
            );

            $synced++;
        }

        return $synced;
    }

    private function downloadFile(string $identity, int $moduleId, string $targetPath, string $accessToken): void
    {
        $response = $this->http->getWithToken(
            "sync/getFileByIdentityInfo?identity={$identity}",
            $accessToken,
            'filemanager'
        );

        $info = $response->json();
        if (!isset($info['file_name'], $info['identity'])) {
            return;
        }

        $content = $this->http->downloadWithToken(
            "sync/downloadFileBinaryForSync?identity={$info['identity']}&moduleId={$moduleId}",
            $accessToken,
            'filemanager'
        );

        $dir = dirname($targetPath);
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        file_put_contents($targetPath, $content);
    }
}
