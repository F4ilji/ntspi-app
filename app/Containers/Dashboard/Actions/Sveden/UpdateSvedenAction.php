<?php

namespace App\Containers\Dashboard\Actions\Sveden;

use App\Containers\Dashboard\Tasks\Sveden\ExtractSvedenArchiveTask;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UpdateSvedenAction
{
    public function __construct(
        private readonly ExtractSvedenArchiveTask $extractTask,
    ) {}

    public function run(UploadedFile $file): array
    {
        $tempPath = storage_path('app/temp/sveden_' . Str::random(16) . '.zip');

        $this->ensureTempDirectory();

        try {
            $file->move(dirname($tempPath), basename($tempPath));

            $result = $this->extractTask->run($tempPath);

            return $result;
        } finally {
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }

    private function ensureTempDirectory(): void
    {
        $tempDir = storage_path('app/temp');

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
    }
}
