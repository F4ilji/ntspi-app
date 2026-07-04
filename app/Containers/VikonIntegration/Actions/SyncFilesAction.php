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
        $filesDir = $modulePath . '/files';

        Log::info('Vikon FM: starting sync', ['module' => $moduleId]);

        $dirs = $this->getUsedDirNames($moduleId, $accessToken);
        Log::info('Vikon FM: directories from FM', ['dirs' => $dirs]);

        foreach ($dirs as $dir) {
            if (!File::isDirectory($filesDir . '/' . $dir)) {
                File::makeDirectory($filesDir . '/' . $dir, 0755, true, true);
            }
        }

        $synced = 0;

        $synced += $this->syncRootDir($moduleId, $accessToken, $filesDir);

        foreach ($dirs as $dir) {
            $synced += $this->syncSubDir($dir, $moduleId, $accessToken, $filesDir);
        }

        $synced += $this->syncNewFiles($moduleId, $accessToken, $filesDir);

        Log::info('Vikon FM: sync done', ['module' => $moduleId, 'synced' => $synced]);
        return "Синхронизировано: {$synced} файлов";
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
        return $body['directories'] ?? [];
    }

    private function syncRootDir(int $moduleId, string $accessToken, string $filesDir): int
    {
        $response = $this->http->getWithToken(
            "sync/getFileNamesFromRootDirectoryByModule?moduleId={$moduleId}",
            $accessToken,
            'filemanager'
        );

        $body = $response->json();
        $files = $body['files'] ?? [];

        if (empty($files)) {
            return 0;
        }

        $synced = 0;
        foreach ($files as $file) {
            $name = $file['n'] ?? null;
            $identity = $file['i'] ?? null;

            if (!$name || !$identity) continue;

            $filePath = $filesDir . '/' . $name;
            $this->downloadAndSave($identity, $moduleId, $filePath, $accessToken);
            $synced++;
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
        $files = $body['files'] ?? [];

        if (empty($files)) {
            return 0;
        }

        $dirPath = $filesDir . '/' . $dir;
        if (!File::isDirectory($dirPath)) {
            File::makeDirectory($dirPath, 0755, true, true);
        }

        $synced = 0;
        foreach ($files as $file) {
            $name = $file['n'] ?? null;
            $identity = $file['i'] ?? null;

            if (!$name || !$identity) continue;

            $filePath = $dirPath . '/' . $name;
            $this->downloadAndSave($identity, $moduleId, $filePath, $accessToken);
            $synced++;
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

            if (empty($body['file_name']) && empty($body['identity'])) {
                break;
            }

            $identity = $body['identity'];
            $filename = $body['file_name'];
            $directory = $body['dir_name'] ?? null;

            $targetDir = $filesDir;
            if ($directory) {
                $targetDir = $filesDir . '/' . $directory;
                if (!File::isDirectory($targetDir)) {
                    File::makeDirectory($targetDir, 0775, true, true);
                }
            }

            $this->downloadAndSave($identity, $moduleId, $targetDir . '/' . $filename, $accessToken);

            $this->http->getWithToken(
                "sync/markNewFileAsLoaded?identity={$identity}&moduleId={$moduleId}",
                $accessToken,
                'filemanager'
            );

            $synced++;
        }

        return $synced;
    }

    private function downloadAndSave(string $identity, int $moduleId, string $targetPath, string $accessToken): void
    {
        try {
            $infoResponse = $this->http->getWithToken(
                "sync/getFileByIdentityInfo?identity={$identity}",
                $accessToken,
                'filemanager'
            );
            $info = $infoResponse->json();

            if (empty($info['identity'])) {
                Log::warning('Vikon FM: no identity in info response', ['identity' => $identity]);
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
        } catch (\Throwable $e) {
            Log::warning('Vikon FM: failed to download file', [
                'identity' => $identity,
                'target' => $targetPath,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
