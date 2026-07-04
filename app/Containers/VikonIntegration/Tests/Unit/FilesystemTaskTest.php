<?php

namespace App\Containers\VikonIntegration\Tests\Unit;

use App\Containers\VikonIntegration\Tasks\FilesystemTask;
use App\Ship\Tests\TestCase;
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
        File::makeDirectory($base . '/common', 0755, true, true);

        file_put_contents($base . '/common/index.php', '<?php echo "old";');

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

    public function test_restore_after_fail_removes_new_and_restores_old(): void
    {
        $base = $this->tempDir . '/module';
        File::makeDirectory($base, 0755, true, true);

        // Simulate partial swap state: _old has backup
        File::makeDirectory($base . '/common', 0755, true, true);
        file_put_contents($base . '/common/index.php', '<?php echo "current";');
        File::makeDirectory($base . '/common_old', 0755, true, true);
        file_put_contents($base . '/common_old/index.php', '<?php echo "original";');

        $result = $this->fs->restoreAfterFail($base, ['common'], 1);

        $this->assertTrue($result);
        $this->assertFileExists($base . '/common/index.php');
        $this->assertStringContainsString('original', file_get_contents($base . '/common/index.php'));
        $this->assertDirectoryDoesNotExist($base . '/common_old');
    }
}
