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

        Log::info('Vikon FM: starting sync', ['module' => $moduleId]);

        $dirIds = $this->getUsedDirNames($moduleId, $accessToken);
        Log::info('Vikon FM: got dir identifiers', ['count' => count($dirIds)]);

        $identities = [];

        $rootFiles = $this->getFileIdentitiesFromRoot($moduleId, $accessToken);
        $identities = array_merge($identities, $rootFiles);
        Log::info('Vikon FM: root file identities', ['count' => count($rootFiles)]);

        foreach ($dirIds as $dirId) {
            $dirFiles = $this->getFileIdentitiesFromSubDir($dirId, $moduleId, $accessToken);
            $identities = array_merge($identities, $dirFiles);
        }
        Log::info('Vikon FM: total file identities', ['count' => count($identities)]);

        $synced = 0;
        foreach ($identities as $identity) {
            $result = $this->downloadByIdentity($identity, $moduleId, $modulePath, $accessToken);
            if ($result) $synced++;
        }

        $newSynced = $this->syncNewFiles($moduleId, $accessToken, $modulePath);

        $total = $synced + $newSynced;
        Log::info('Vikon FM: sync complete', ['downloaded' => $synced, 'new' => $newSynced]);
        return "Синхронизировано: {$total} файлов";
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

    private function getFileIdentitiesFromRoot(int $moduleId, string $accessToken): array
    {
        $response = $this->http->getWithToken(
            "sync/getFileNamesFromRootDirectoryByModule?moduleId={$moduleId}",
            $accessToken,
            'filemanager'
        );
        $body = $response->json();
        $files = $body['files'] ?? [];

        return array_map(fn($f) => $f['i'], array_filter($files, fn($f) => !empty($f['i'])));
    }

    private function getFileIdentitiesFromSubDir(string $dirId, int $moduleId, string $accessToken): array
    {
        $response = $this->http->getWithToken(
            "sync/getFileNamesFromSubDirectoryByModule?dir={$dirId}&moduleId={$moduleId}",
            $accessToken,
            'filemanager'
        );
        $body = $response->json();
        $files = $body['files'] ?? [];

        return array_map(fn($f) => $f['i'], array_filter($files, fn($f) => !empty($f['i'])));
    }

    private function downloadByIdentity(string $identity, int $moduleId, string $modulePath, string $accessToken): bool
    {
        try {
            $infoResp = $this->http->getWithToken(
                "sync/getFileByIdentityInfo?identity={$identity}",
                $accessToken,
                'filemanager'
            );
            $info = $infoResp->json();

            if (empty($info['identity']) || empty($info['file_name'])) {
                return false;
            }

            $dirName = $info['dir_name'] ?? null;
            $targetDir = $modulePath;
            if ($dirName) {
                $targetDir = $modulePath . '/' . $dirName;
            }

            $content = $this->http->downloadWithToken(
                "sync/downloadFileBinaryForSync?identity={$info['identity']}&moduleId={$moduleId}",
                $accessToken,
                'filemanager'
            );

            if (!File::isDirectory($targetDir)) {
                File::makeDirectory($targetDir, 0755, true, true);
            }

            $targetPath = $targetDir . '/' . $info['file_name'];
            file_put_contents($targetPath, $content);
            return true;
        } catch (\Throwable $e) {
            Log::warning('Vikon FM: download failed', ['identity' => $identity, 'error' => $e->getMessage()]);
            return false;
        }
    }

    private function syncNewFiles(int $moduleId, string $accessToken, string $modulePath): int
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

            $targetDir = $directory ? $modulePath . '/' . $directory : $modulePath;

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
