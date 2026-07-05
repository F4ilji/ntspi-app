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
        File::makeDirectory($modulePath, 0755, true, true);
        File::makeDirectory($filesDir, 0755, true, true);

        Log::info('Vikon FM: starting sync', ['module' => $moduleId]);

        $dirIds = $this->getUsedDirNames($moduleId, $accessToken);
        Log::info('Vikon FM: dir identifiers', ['count' => count($dirIds)]);

        $synced = 0;

        $synced += $this->syncRootDir($moduleId, $filesDir, $accessToken);

        foreach ($dirIds as $dirId) {
            $synced += $this->syncSubDir($dirId, $moduleId, $filesDir, $accessToken);
        }

        $newSynced = $this->syncNewFiles($moduleId, $accessToken, $filesDir);

        $total = $synced + $newSynced;
        Log::info('Vikon FM: sync complete', ['downloaded' => $synced, 'new' => $newSynced]);
        return "Синхронизировано: {$total} файлов";
    }

    private function getModuleName(int $moduleId): string
    {
        $modules = config('vikon.modules');
        return $modules[$moduleId]['path'] ?? throw new \RuntimeException("Unknown module: {$moduleId}");
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
                file_put_contents($filesDir . '/' . $name, $content);
                $synced++;
            } catch (\Throwable $e) {
                Log::warning('Vikon FM: root download failed', ['identity' => $identity, 'error' => $e->getMessage()]);
            }
        }
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

            if (empty($body['file_name']) && empty($body['identity'])) {
                break;
            }

            $identity = $body['identity'];
            $filename = $body['file_name'];
            $directory = $body['dir_name'] ?? null;

            $targetDir = $directory ? $filesDir . '/' . $directory : $filesDir;

            $content = $this->http->downloadWithToken(
                "sync/downloadFileBinary?identity={$identity}",
                $accessToken,
                'filemanager'
            );

            if (!File::isDirectory($targetDir)) {
                File::makeDirectory($targetDir, 0755, true, true);
            }

            file_put_contents($targetDir . '/' . $filename, $content);

            $this->http->getWithToken(
                "sync/markNewFileAsLoaded?identity={$identity}&moduleId={$moduleId}",
                $accessToken,
                'filemanager'
            );

            $synced++;
        }

        return $synced;
    }
}
