# VikonPartialUpdates Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use compose:subagent (recommended) or compose:execute to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement partial module updates (per-part: common, struct, document...) in VikonIntegration with full backend support and Vue UI.

**Architecture:** Backend-driven polling — frontend sends one request, backend executes the 4-step flow (request → poll → check → download), returns result. Atomic swap with `_new`/`_old` suffixes for crash safety. Separate Action for orchestration, Task for polling loop.

**Tech Stack:** Laravel 10, PHP 8.1+, Vue 3 Composition API, Inertia.js, PHPUnit

## Global Constraints

- Porto Architecture: Route → Controller → Action → Task
- All PHP code in `app/Containers/VikonIntegration/`
- Tests in `app/Containers/VikonIntegration/Tests/Unit/` and `Tests/Feature/`
- Docker constraint: PHP/composer commands via `docker exec ntspi-php`
- Code in English, comments in English
- No Options API in Vue — Composition API only

---

### Task 1: Add `atomicSwap()` to FilesystemTask

**Covers:** [S3], [S7]

**Files:**
- Modify: `app/Containers/VikonIntegration/Tasks/FilesystemTask.php`
- Test: `app/Containers/VikonIntegration/Tests/Unit/FilesystemTaskTest.php`

**Interfaces:**
- Consumes: existing `isPathSafe()` method
- Produces: `atomicSwap(string $newEntryPath, string $currentEntryPath, string $baseDir, int $moduleId): bool`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Unit;

use App\Containers\VikonIntegration\Tasks\FilesystemTask;
use Tests\TestCase;
use Illuminate\Support\Facades\File;

class FilesystemTaskTest extends TestCase
{
    private string $tempDir;
    private FilesystemTask $fs;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . '/vikon_test_' . uniqid();
        File::makeDirectory($this->tempDir, 0755, true, true);
        $this->fs = new FilesystemTask();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->tempDir);
        parent::tearDown();
    }

    public function test_atomic_swap_replaces_existing_entry(): void
    {
        $base = $this->tempDir . '/module';
        File::makeDirectory($base, 0755, true, true);

        // Current entry exists
        file_put_contents($base . '/common/index.php', '<?php echo "old";');

        // New entry from extracted ZIP
        $newDir = $this->tempDir . '/extracted/common';
        File::makeDirectory($newDir, 0755, true, true);
        file_put_contents($newDir . '/index.php', '<?php echo "new";');

        $result = $this->fs->atomicSwap(
            $newDir,
            $base . '/common',
            $base,
            1
        );

        $this->assertTrue($result);
        $this->assertFileExists($base . '/common/index.php');
        $this->assertStringContainsString('new', file_get_contents($base . '/common/index.php'));
        $this->assertDirectoryExists($base . '/common_old');
        $this->assertDirectoryDoesNotExist($base . '/common_new');
    }

    public function test_atomic_swap_creates_new_entry_when_not_exists(): void
    {
        $base = $this->tempDir . '/module';
        File::makeDirectory($base, 0755, true, true);

        $newDir = $this->tempDir . '/extracted/assets';
        File::makeDirectory($newDir, 0755, true, true);
        file_put_contents($newDir . '/style.css', 'body {}');

        $result = $this->fs->atomicSwap(
            $newDir,
            $base . '/assets',
            $base,
            1
        );

        $this->assertTrue($result);
        $this->assertFileExists($base . '/assets/style.css');
        $this->assertDirectoryDoesNotExist($base . '/assets_old');
    }

    public function test_atomic_swap_returns_false_on_path_traversal(): void
    {
        $result = $this->fs->atomicSwap(
            '/etc/passwd',
            $this->tempDir . '/module/etc',
            $this->tempDir . '/module',
            1
        );

        $this->assertFalse($result);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `docker exec ntspi-php php artisan test --filter=FilesystemTaskTest`
Expected: FAIL with "Call to undefined method atomicSwap"

- [ ] **Step 3: Write minimal implementation**

Add to `app/Containers/VikonIntegration/Tasks/FilesystemTask.php`:

```php
public function atomicSwap(
    string $newEntryPath,
    string $currentEntryPath,
    string $baseDir,
    int $moduleId
): bool {
    if (!$this->isPathSafe($newEntryPath, $baseDir) || !$this->isPathSafe($currentEntryPath, $baseDir)) {
        return false;
    }

    if (!File::exists($newEntryPath)) {
        return false;
    }

    $isFile = is_file($newEntryPath);
    $newPostfix = $currentEntryPath . '_new';
    $oldPostfix = $currentEntryPath . '_old';

    if (File::exists($currentEntryPath)) {
        // Step 1: Remove stale _new if exists
        if (File::exists($newPostfix)) {
            $isFile ? File::delete($newPostfix) : File::deleteDirectory($newPostfix);
        }

        // Step 2: Move new → _new
        if (!$this->moveEntry($newEntryPath, $newPostfix, $isFile)) {
            return false;
        }

        // Step 3: Remove stale _old if exists
        if (File::exists($oldPostfix)) {
            $isFile ? File::delete($oldPostfix) : File::deleteDirectory($oldPostfix);
        }

        // Step 4: Move current → _old
        if (!$this->moveEntry($currentEntryPath, $oldPostfix, $isFile)) {
            return false;
        }

        // Step 5: Move _new → current
        return $this->moveEntry($newPostfix, $currentEntryPath, $isFile);
    }

    // Entry doesn't exist — move directly
    return $this->moveEntry($newEntryPath, $currentEntryPath, $isFile);
}

private function moveEntry(string $source, string $dest, bool $isFile): bool
{
    if ($isFile) {
        $parent = dirname($dest);
        if (!File::isDirectory($parent)) {
            File::makeDirectory($parent, 0755, true, true);
        }
        return rename($source, $dest);
    }
    return rename($source, $dest);
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `docker exec ntspi-php php artisan test --filter=FilesystemTaskTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Containers/VikonIntegration/Tasks/FilesystemTask.php app/Containers/VikonIntegration/Tests/Unit/FilesystemTaskTest.php
git commit -m "feat(vikon): add atomicSwap to FilesystemTask for crash-safe updates"
```

---

### Task 2: Create PollPartStatusTask

**Covers:** [S3], [S8]

**Files:**
- Create: `app/Containers/VikonIntegration/Tasks/PollPartStatusTask.php`
- Test: `app/Containers/VikonIntegration/Tests/Unit/PollPartStatusTaskTest.php`

**Interfaces:**
- Consumes: `HttpTask::getWithToken()`
- Produces: `run(string $operationIdentity, string $accessToken): array` returning `['status' => 'completed'|'failed'|'timeout']`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Unit;

use App\Containers\VikonIntegration\Tasks\PollPartStatusTask;
use App\Containers\VikonIntegration\Tasks\HttpTask;
use Tests\TestCase;
use Mockery;

class PollPartStatusTaskTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_completed_when_status_is_completed(): void
    {
        $http = Mockery::mock(HttpTask::class);
        $response = Mockery::mock();
        $response->shouldReceive('json')->once()->andReturn(['status' => 'completed']);

        $http->shouldReceive('getWithToken')
            ->once()
            ->with(
                Mockery::on(fn($endpoint) => str_contains($endpoint, 'getStatusPartGeneration')),
                'test-token',
                'api'
            )
            ->andReturn($response);

        $task = new PollPartStatusTask($http, 3, 50);
        $result = $task->run('op-123', 'test-token');

        $this->assertEquals('completed', $result['status']);
    }

    public function test_returns_failed_when_status_is_failed(): void
    {
        $http = Mockery::mock(HttpTask::class);
        $response = Mockery::mock();
        $response->shouldReceive('json')->once()->andReturn([
            'status' => 'failed',
            'message' => 'Generation error',
        ]);

        $http->shouldReceive('getWithToken')
            ->once()
            ->andReturn($response);

        $task = new PollPartStatusTask($http, 3, 50);
        $result = $task->run('op-123', 'test-token');

        $this->assertEquals('failed', $result['status']);
        $this->assertEquals('Generation error', $result['error'] ?? null);
    }

    public function test_returns_timeout_after_max_attempts(): void
    {
        $http = Mockery::mock(HttpTask::class);
        $response = Mockery::mock();
        $response->shouldReceive('json')->andReturn(['status' => 'pending']);

        $http->shouldReceive('getWithToken')
            ->times(3)
            ->andReturn($response);

        $task = new PollPartStatusTask($http, 0, 3); // interval=0, max=3
        $result = $task->run('op-123', 'test-token');

        $this->assertEquals('timeout', $result['status']);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `docker exec ntspi-php php artisan test --filter=PollPartStatusTaskTest`
Expected: FAIL with "Class not found"

- [ ] **Step 3: Write minimal implementation**

Create `app/Containers/VikonIntegration/Tasks/PollPartStatusTask.php`:

```php
<?php

namespace App\Containers\VikonIntegration\Tasks;

use Illuminate\Support\Facades\Log;

class PollPartStatusTask
{
    public function __construct(
        private readonly HttpTask $http,
        private readonly int $interval,
        private readonly int $maxAttempts,
    ) {}

    public function run(string $operationIdentity, string $accessToken): array
    {
        for ($attempt = 0; $attempt < $this->maxAttempts; $attempt++) {
            $response = $this->http->getWithToken(
                "pull_updates/getStatusPartGenerationByNewCoreJson?operation_identity={$operationIdentity}",
                $accessToken
            );

            $body = $response->json();
            $status = $body['status'] ?? 'unknown';

            Log::info('Vikon poll part status', [
                'operation' => $operationIdentity,
                'status' => $status,
                'attempt' => $attempt + 1,
            ]);

            if ($status === 'completed') {
                return ['status' => 'completed'];
            }

            if ($status === 'failed') {
                return [
                    'status' => 'failed',
                    'error' => $body['message'] ?? 'Unknown error',
                ];
            }

            if ($attempt < $this->maxAttempts - 1) {
                sleep($this->interval);
            }
        }

        return ['status' => 'timeout'];
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `docker exec ntspi-php php artisan test --filter=PollPartStatusTaskTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Containers/VikonIntegration/Tasks/PollPartStatusTask.php app/Containers/VikonIntegration/Tests/Unit/PollPartStatusTaskTest.php
git commit -m "feat(vikon): add PollPartStatusTask for part update polling"
```

---

### Task 3: Add parts config to vikon.php

**Covers:** [S8]

**Files:**
- Modify: `config/vikon.php`
- Modify: `app/Containers/VikonIntegration/Providers/VikonServiceProvider.php` (if needed for new config)

**Interfaces:**
- Consumes: existing config structure
- Produces: `config('vikon.parts')`, `config('vikon.poll_interval')`, `config('vikon.poll_max_attempts')`

- [ ] **Step 1: Read current config**

Read `config/vikon.php` to see current structure.

- [ ] **Step 2: Add parts configuration**

Add to `config/vikon.php` before the closing `];`:

```php
    'parts' => [
        1 => ['common', 'struct', 'document', 'education', 'managers', 'employees', 'objects', 'paid_edu', 'budget', 'vacant', 'grants', 'inter', 'catering', 'eduStandarts', 'corruption', 'antiterrorism'],
        2 => ['abitur'],
        6 => ['general', 'structure', 'faq', 'procedures', 'results-and-reports', 'plans', 'survey'],
    ],
    'poll_interval' => (int) env('VIKON_POLL_INTERVAL', 3),
    'poll_max_attempts' => (int) env('VIKON_POLL_MAX_ATTEMPTS', 50),
```

- [ ] **Step 3: Verify config loads**

Run: `docker exec ntspi-php php artisan tinker --execute="dump(config('vikon.parts'));"`
Expected: Array with module IDs as keys

- [ ] **Step 4: Commit**

```bash
git add config/vikon.php
git commit -m "feat(vikon): add parts config for partial updates"
```

---

### Task 4: Create UpdatePartAction

**Covers:** [S3], [S5], [S6], [S7]

**Files:**
- Create: `app/Containers/VikonIntegration/Actions/UpdatePartAction.php`
- Test: `app/Containers/VikonIntegration/Tests/Unit/UpdatePartActionTest.php`

**Interfaces:**
- Consumes: `HttpTask`, `FilesystemTask::atomicSwap()`, `PollPartStatusTask`
- Produces: `run(int $moduleId, string $part, string $accessToken): array`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Unit;

use App\Containers\VikonIntegration\Actions\UpdatePartAction;
use App\Containers\VikonIntegration\Tasks\HttpTask;
use App\Containers\VikonIntegration\Tasks\FilesystemTask;
use App\Containers\VikonIntegration\Tasks\PollPartStatusTask;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\File;

class UpdatePartActionTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . '/vikon_update_test_' . uniqid();
        File::makeDirectory($this->tempDir, 0755, true, true);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->tempDir);
        Mockery::close();
        parent::tearDown();
    }

    public function test_rejects_invalid_module_id(): void
    {
        $http = Mockery::mock(HttpTask::class);
        $fs = Mockery::mock(FilesystemTask::class);
        $poll = Mockery::mock(PollPartStatusTask::class);

        $action = new UpdatePartAction($http, $fs, $poll, $this->tempDir, $this->tempDir, [
            1 => ['path' => 'sveden', 'allowed_folders' => ['common']],
        ]);

        $this->expectException(\RuntimeException::class);
        $action->run(999, 'common', 'token');
    }

    public function test_rejects_invalid_part(): void
    {
        $http = Mockery::mock(HttpTask::class);
        $fs = Mockery::mock(FilesystemTask::class);
        $poll = Mockery::mock(PollPartStatusTask::class);

        $action = new UpdatePartAction($http, $fs, $poll, $this->tempDir, $this->tempDir, [
            1 => ['path' => 'sveden', 'allowed_folders' => ['common']],
        ]);

        $this->expectException(\RuntimeException::class);
        $action->run(1, 'nonexistent', 'token');
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `docker exec ntspi-php php artisan test --filter=UpdatePartActionTest`
Expected: FAIL with "Class not found"

- [ ] **Step 3: Write minimal implementation**

Create `app/Containers/VikonIntegration/Actions/UpdatePartAction.php`:

```php
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

        if (!in_array($part, config('vikon.parts', [])[$moduleId] ?? [], true)) {
            throw new \RuntimeException("Invalid part '{$part}' for module {$moduleId}");
        }

        Log::info('Vikon: starting part update', ['module' => $moduleId, 'part' => $part]);

        // Step 1: Request generation
        $genResponse = $this->http->postWithToken(
            'pull_updates/requestGeneratePartByNewCoreJson',
            $accessToken,
            ['part' => $part]
        );
        $genBody = $genResponse->json();

        if (empty($genBody['operation_identity'])) {
            throw new \RuntimeException('Failed to request part generation: ' . ($genBody['message'] ?? 'Unknown'));
        }

        $operationIdentity = $genBody['operation_identity'];
        $ttl = $genBody['ttl'] ?? 120;

        Log::info('Vikon: part generation requested', ['operation' => $operationIdentity, 'ttl' => $ttl]);

        // Step 2: Poll status
        $pollResult = $this->pollStatus->run($operationIdentity, $accessToken);

        if ($pollResult['status'] !== 'completed') {
            $error = $pollResult['error'] ?? $pollResult['status'];
            throw new \RuntimeException("Part generation failed: {$error}");
        }

        // Step 3: Check result
        $checkResponse = $this->http->postWithToken(
            'pull_updates/checkPartGenerationByNewCoreResultJson',
            $accessToken,
            ['operation_identity' => $operationIdentity, 'part' => $part]
        );
        $checkBody = $checkResponse->json();

        if (empty($checkBody['success'])) {
            throw new \RuntimeException('Part not ready: ' . ($checkBody['message'] ?? 'Unknown'));
        }

        // Step 4: Download and apply
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

        $modulePath = $this->basePath . '/' . $config['path'];
        $moduleConfig = $this->modulesConfig[$moduleId];

        // Determine which module owns this part
        $ownerModuleId = $moduleId;
        if ($part !== 'abitur') {
            // For non-ABITUR parts, apply to the module that owns them
            $ownerModuleId = $moduleId;
        }

        $syncedCount = $this->applyPart($part, $tempPath, $modulePath, $ownerModuleId, $moduleConfig);

        // Clean temp
        File::deleteDirectory($tempPath);

        Log::info('Vikon: part update complete', ['module' => $moduleId, 'part' => $part, 'synced' => $syncedCount]);

        return [
            'success' => true,
            "message" => "Part '{$part}' updated successfully.",
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
            return $this->applyAbiturPart($tempPath, $modulePath, $moduleId, $moduleConfig);
        }

        return $this->applyRegularPart($part, $tempPath, $modulePath, $moduleId, $moduleConfig);
    }

    private function applyRegularPart(
        string $part,
        string $tempPath,
        string $modulePath,
        int $moduleId,
        array $moduleConfig
    ): int {
        $partSource = $tempPath . '/' . $part;
        $partTarget = $modulePath . '/' . $part;

        if (!File::exists($partSource)) {
            // ZIP might have the part nested under a directory
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
        int $moduleId,
        array $moduleConfig
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
```

- [ ] **Step 4: Run test to verify it passes**

Run: `docker exec ntspi-php php artisan test --filter=UpdatePartActionTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Containers/VikonIntegration/Actions/UpdatePartAction.php app/Containers/VikonIntegration/Tests/Unit/UpdatePartActionTest.php
git commit -m "feat(vikon): add UpdatePartAction for partial module updates"
```

---

### Task 5: Add `restoreAfterFail()` to FilesystemTask

**Covers:** [S7]

**Files:**
- Modify: `app/Containers/VikonIntegration/Tasks/FilesystemTask.php`
- Test: `app/Containers/VikonIntegration/Tests/Unit/FilesystemTaskTest.php` (add test)

**Interfaces:**
- Consumes: existing `isPathSafe()`, `atomicSwap()`
- Produces: `restoreAfterFail(string $modulePath, array $allowedEntries, int $moduleId): bool`

- [ ] **Step 1: Add failing test**

Append to `app/Containers/VikonIntegration/Tests/Unit/FilesystemTaskTest.php`:

```php
public function test_restore_after_fail_removes_new_and_restores_old(): void
{
    $base = $this->tempDir . '/module';
    File::makeDirectory($base, 0755, true, true);

    // Simulate partial swap state: original exists, _old has backup
    file_put_contents($base . '/common/index.php', '<?php echo "current";');
    file_put_contents($base . '/common_old/index.php', '<?php echo "original";');

    $result = $this->fs->restoreAfterFail($base, ['common'], 1);

    $this->assertTrue($result);
    $this->assertFileExists($base . '/common/index.php');
    $this->assertStringContainsString('original', file_get_contents($base . '/common/index.php'));
    $this->assertDirectoryDoesNotExist($base . '/common_old');
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `docker exec ntspi-php php artisan test --filter=FilesystemTaskTest::test_restore_after_fail`
Expected: FAIL with "Call to undefined method restoreAfterFail"

- [ ] **Step 3: Write implementation**

Add to `app/Containers/VikonIntegration/Tasks/FilesystemTask.php`:

```php
public function restoreAfterFail(string $modulePath, array $allowedEntries, int $moduleId): bool
{
    $entries = File::directories($modulePath);
    $entries = array_merge($entries, File::files($modulePath));

    foreach ($entries as $entryPath) {
        $name = basename($entryPath);

        // Remove _new entries (they were never applied)
        if (str_ends_with($name, '_new')) {
            $baseName = substr($name, 0, -4);
            if (in_array($baseName, $allowedEntries, true)) {
                if (is_dir($entryPath)) {
                    File::deleteDirectory($entryPath);
                } else {
                    File::delete($entryPath);
                }
            }
        }

        // Restore _old entries back to original name
        if (str_ends_with($name, '_old')) {
            $baseName = substr($name, 0, -4);
            if (in_array($baseName, $allowedEntries, true)) {
                $originalPath = $modulePath . '/' . $baseName;
                if (File::exists($originalPath)) {
                    if (is_dir($originalPath)) {
                        File::deleteDirectory($originalPath);
                    } else {
                        File::delete($originalPath);
                    }
                }
                rename($entryPath, $originalPath);
            }
        }
    }

    return true;
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `docker exec ntspi-php php artisan test --filter=FilesystemTaskTest`
Expected: PASS (all 4 tests)

- [ ] **Step 5: Commit**

```bash
git add app/Containers/VikonIntegration/Tasks/FilesystemTask.php app/Containers/VikonIntegration/Tests/Unit/FilesystemTaskTest.php
git commit -m "feat(vikon): add restoreAfterFail for crash recovery in FilesystemTask"
```

---

### Task 6: Create UpdatePartRequest and Controller route

**Covers:** [S5]

**Files:**
- Create: `app/Containers/VikonIntegration/UI/WEB/Requests/UpdatePartRequest.php`
- Modify: `app/Containers/VikonIntegration/UI/WEB/Controllers/VikonController.php`
- Modify: `app/Containers/VikonIntegration/UI/WEB/Routes/web.php`

**Interfaces:**
- Consumes: `UpdatePartAction::run()`
- Produces: `POST /dashboard/vikon-updates/update-part` endpoint

- [ ] **Step 1: Create UpdatePartRequest**

Create `app/Containers/VikonIntegration/UI/WEB/Requests/UpdatePartRequest.php`:

```php
<?php

namespace App\Containers\VikonIntegration\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'module_id' => ['required', 'integer', 'in:1,2,6'],
            'part' => ['required', 'string', 'max:50'],
        ];
    }
}
```

- [ ] **Step 2: Add updatePart method to VikonController**

Add to `app/Containers/VikonIntegration/UI/WEB/Controllers/VikonController.php`:

```php
use App\Containers\VikonIntegration\Actions\UpdatePartAction;
use App\Containers\VikonIntegration\UI\WEB\Requests\UpdatePartRequest;
```

Add property and constructor injection:

```php
private readonly UpdatePartAction $updatePart,
```

Add method:

```php
public function updatePart(UpdatePartRequest $request): JsonResponse
{
    $token = Session::get('vikon_access_token');
    if (!$token) {
        return response()->json(['success' => false, 'requires_auth' => true], 401);
    }

    try {
        $result = $this->updatePart->run(
            $request->validated('module_id'),
            $request->validated('part'),
            $token
        );
        return response()->json($result);
    } catch (\Throwable $e) {
        Log::error('Vikon part update failed', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
```

- [ ] **Step 3: Add route**

Add to `app/Containers/VikonIntegration/UI/WEB/Routes/web.php`:

```php
Route::post('/update-part', [VikonController::class, 'updatePart'])->name('update-part');
```

- [ ] **Step 4: Verify route exists**

Run: `docker exec ntspi-php php artisan route:list --name=dashboard.vikon-updates`
Expected: Shows `update-part` route

- [ ] **Step 5: Commit**

```bash
git add app/Containers/VikonIntegration/UI/WEB/Requests/UpdatePartRequest.php app/Containers/VikonIntegration/UI/WEB/Controllers/VikonController.php app/Containers/VikonIntegration/UI/WEB/Routes/web.php
git commit -m "feat(vikon): add updatePart endpoint with request validation"
```

---

### Task 7: Update Vue frontend with part selection UI

**Covers:** [S4]

**Files:**
- Modify: `resources/js/Pages/Dashboard/VikonUpdates/Index.vue`
- Modify: `app/Containers/VikonIntegration/UI/WEB/Controllers/VikonController.php` (add parts to props)

**Interfaces:**
- Consumes: `POST /dashboard/vikon-updates/update-part` endpoint
- Produces: UI with module/part selection and update button

- [ ] **Step 1: Add parts to controller props**

In `VikonController::index()` and `oauthCallback()`, add to the inertia render:

```php
'parts' => config('vikon.parts'),
```

- [ ] **Step 2: Update Index.vue**

Read current `resources/js/Pages/Dashboard/VikonUpdates/Index.vue` and add:

1. New prop: `parts`
2. New reactive state: `selectedPart`, `updatingPart`, `partResult`
3. New method: `updatePart(moduleId, part)`
4. UI section: dropdown for part selection per module, update button, progress indicator

Key additions to the `<script setup>`:

```javascript
const props = defineProps({
    // ... existing props
    parts: Object,
});

const selectedPart = ref({});
const updatingPart = ref(null);
const partResult = ref(null);

async function updatePart(moduleId, part) {
    updatingPart.value = `${moduleId}-${part}`;
    partResult.value = null;

    try {
        const res = await fetch('/dashboard/vikon-updates/update-part', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ module_id: moduleId, part }),
        });

        const data = await res.json();
        partResult.value = data;
    } catch (e) {
        partResult.value = { success: false, message: e.message };
    } finally {
        updatingPart.value = null;
    }
}
```

Template additions (inside the module list):

```html
<div v-if="parts && parts[moduleId]" class="mt-2">
    <select v-model="selectedPart[moduleId]" class="text-sm border rounded px-2 py-1">
        <option value="">Select part...</option>
        <option v-for="p in parts[moduleId]" :key="p" :value="p">{{ p }}</option>
    </select>
    <button
        @click="updatePart(moduleId, selectedPart[moduleId])"
        :disabled="!selectedPart[moduleId] || updatingPart"
        class="ml-2 text-sm bg-blue-500 text-white px-3 py-1 rounded disabled:opacity-50"
    >
        <span v-if="updatingPart === `${moduleId}-${selectedPart[moduleId]}`">Updating...</span>
        <span v-else>Update Part</span>
    </button>
</div>
```

- [ ] **Step 3: Verify Vue compiles**

Run: `npm run build`
Expected: No errors

- [ ] **Step 4: Commit**

```bash
git add resources/js/Pages/Dashboard/VikonUpdates/Index.vue app/Containers/VikonIntegration/UI/WEB/Controllers/VikonController.php
git commit -m "feat(vikon): add part selection UI to Vue dashboard"
```

---

### Task 8: Integration test for full flow

**Covers:** [S3], [S5], [S7]

**Files:**
- Create: `app/Containers/VikonIntegration/Tests/Feature/UpdatePartTest.php`

**Interfaces:**
- Consumes: all previous tasks
- Produces: Feature test hitting the HTTP endpoint

- [ ] **Step 1: Create feature test**

Create `app/Containers/VikonIntegration/Tests/Feature/UpdatePartTest.php`:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Session;

class UpdatePartTest extends TestCase
{
    public function test_update_part_requires_authentication(): void
    {
        $response = $this->postJson('/dashboard/vikon-updates/update-part', [
            'module_id' => 1,
            'part' => 'common',
        ]);

        $response->assertStatus(401);
    }

    public function test_update_part_validates_module_id(): void
    {
        Session::put('vikon_access_token', 'test-token');

        $response = $this->postJson('/dashboard/vikon-updates/update-part', [
            'module_id' => 999,
            'part' => 'common',
        ]);

        $response->assertStatus(422);
    }

    public function test_update_part_validates_part(): void
    {
        Session::put('vikon_access_token', 'test-token');

        $response = $this->postJson('/dashboard/vikon-updates/update-part', [
            'module_id' => 1,
            'part' => '',
        ]);

        $response->assertStatus(422);
    }
}
```

- [ ] **Step 2: Run test**

Run: `docker exec ntspi-php php artisan test --filter=UpdatePartTest`
Expected: PASS

- [ ] **Step 3: Commit**

```bash
git add app/Containers/VikonIntegration/Tests/Feature/UpdatePartTest.php
git commit -m "test(vikon): add feature tests for part update endpoint"
```
