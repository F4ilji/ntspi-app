<?php

namespace App\Containers\VikonIntegration\Actions;

use App\Containers\VikonIntegration\Tasks\HttpTask;
use App\Containers\VikonIntegration\Tasks\FilesystemTask;
use App\Containers\VikonIntegration\Tasks\PollPartStatusTask;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class UpdatePartAction
{
    public function __construct(
        private readonly HttpTask $http,
        private readonly FilesystemTask $fs,
        private readonly PollPartStatusTask $pollStatus,
        private readonly string $storagePath,
        private readonly string $basePath,
        private readonly array $modulesConfig,
    ) {}

    public function run(int $moduleId, string $part, string $accessToken): array
    {
        $config = $this->modulesConfig[$moduleId] ?? throw new \RuntimeException("Unknown module: {$moduleId}");

        Log::info('Vikon: starting part update', ['module' => $moduleId, 'part' => $part]);

        // Step 1: Request generation
        $genResponse = $this->http->postWithToken(
            'pull_updates/generatePartByNewCoreJson',
            $accessToken,
            ['part' => $part]
        );
        $genBody = $genResponse->json();

        if (empty($genBody['operation_identity'])) {
            throw new \RuntimeException('Failed to request part generation: ' . ($genBody['message'] ?? 'Unknown'));
        }

        $operationIdentity = $genBody['operation_identity'];

        Log::info('Vikon: part generation requested', ['operation' => $operationIdentity]);

        // Step 2: Poll status
        $pollResult = $this->pollStatus->run($operationIdentity, $accessToken);

        if ($pollResult['status'] !== 'completed') {
            $error = $pollResult['error'] ?? $pollResult['status'];
            throw new \RuntimeException("Part generation failed: {$error}");
        }

        // Step 3: Check result (API returns HTTP 200 on success, no JSON body needed)
        $checkResponse = $this->http->getWithToken(
            "pull_updates/checkPartGenerationByNewCoreResultJson?operation_identity={$operationIdentity}&part={$part}",
            $accessToken
        );

        if ($checkResponse->failed()) {
            $body = $checkResponse->json([]);
            throw new \RuntimeException('Part not ready: ' . ($body['message'] ?? 'HTTP ' . $checkResponse->status()));
        }

        // Step 4: Download ZIP
        $zipContent = $this->http->downloadWithToken(
            "pull_updates/downloadPartByNewCoreResult?operation_identity={$operationIdentity}&part={$part}",
            $accessToken
        );

        $tempPath = $this->storagePath . '/temp/' . $config['path'] . '_part';
        File::makeDirectory($tempPath, 0755, true, true);

        $zipFile = $tempPath . '/part.zip';
        file_put_contents($zipFile, $zipContent);

        $zip = new ZipArchive();
        if ($zip->open($zipFile) !== true) {
            throw new \RuntimeException('Failed to open part ZIP');
        }
        $zip->extractTo($tempPath);
        $zip->close();
        File::delete($zipFile);

        // Validate extracted file types
        $blocked = $this->fs->validateFileTypes($tempPath);
        if (!empty($blocked)) {
            File::deleteDirectory($tempPath);
            throw new \RuntimeException('Blocked files in ZIP: ' . implode(', ', $blocked));
        }

        $modulePath = $this->basePath . '/' . $config['path'];

        try {
            // Step 5: Apply
            $syncedCount = $this->applyPart($part, $tempPath, $modulePath, $moduleId, $config);
        } finally {
            // Step 6: Clean temp (always runs, even on exception)
            File::deleteDirectory($tempPath);
        }

        Log::info('Vikon: part update complete', ['module' => $moduleId, 'part' => $part, 'synced' => $syncedCount]);

        return [
            'success' => true,
            'message' => "Part '{$part}' updated successfully.",
            'synced_count' => $syncedCount,
        ];
    }

    private function applyPart(
        string $part,
        string $tempPath,
        string $modulePath,
        int $moduleId,
        array $moduleConfig
    ): int {
        if ($part === 'abitur') {
            return $this->applyAbiturPart($tempPath, $modulePath, $moduleId);
        }

        return $this->applyRegularPart($part, $tempPath, $modulePath, $moduleId);
    }

    private function applyRegularPart(
        string $part,
        string $tempPath,
        string $modulePath,
        int $moduleId
    ): int {
        $partSource = $tempPath . '/' . $part;
        $partTarget = $modulePath . '/' . $part;

        if (!File::exists($partSource)) {
            $extractedDirs = File::directories($tempPath);
            if (!empty($extractedDirs)) {
                $partSource = $extractedDirs[0] . '/' . $part;
            }
        }

        if (!File::exists($partSource)) {
            throw new \RuntimeException("Part directory not found in ZIP: {$part}");
        }

        $result = $this->fs->atomicSwap($partSource, $partTarget, $modulePath, $moduleId);
        if (!$result) {
            $this->fs->restoreAfterFail($modulePath, [$part], $moduleId);
            throw new \RuntimeException("Failed to apply part: {$part}");
        }

        return 1;
    }

    private function applyAbiturPart(
        string $tempPath,
        string $modulePath,
        int $moduleId
    ): int {
        $abiturSource = $tempPath . '/abitur';
        if (!File::exists($abiturSource)) {
            $extractedDirs = File::directories($tempPath);
            if (!empty($extractedDirs)) {
                $abiturSource = $extractedDirs[0] . '/abitur';
            }
        }

        if (!File::exists($abiturSource)) {
            throw new \RuntimeException('ABITUR directory not found in ZIP');
        }

        $entries = File::allFiles($abiturSource);
        $synced = 0;

        foreach ($entries as $file) {
            $relative = ltrim(str_replace($abiturSource, '', $file->getPathname()), '/');
            $targetPath = $modulePath . '/' . $relative;
            $targetDir = dirname($targetPath);

            if (!File::isDirectory($targetDir)) {
                File::makeDirectory($targetDir, 0755, true, true);
            }

            $result = $this->fs->atomicSwap(
                $file->getPathname(),
                $targetPath,
                $modulePath,
                $moduleId
            );

            if (!$result) {
                $this->fs->restoreAfterFail($modulePath, ['abitur'], $moduleId);
                throw new \RuntimeException("Failed to sync ABITUR file: {$relative}");
            }

            $synced++;
        }

        return $synced;
    }
}
