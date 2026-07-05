<?php

namespace App\Containers\VikonIntegration\Tests\Unit;

use App\Containers\VikonIntegration\Actions\UpdatePartAction;
use App\Containers\VikonIntegration\Tasks\HttpTask;
use App\Containers\VikonIntegration\Tasks\FilesystemTask;
use App\Containers\VikonIntegration\Tasks\PollPartStatusTask;
use App\Ship\Tests\TestCase;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\File;
use Mockery;

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

    private function mockResponse(array $data): Response
    {
        $response = Mockery::mock(Response::class);
        $response->shouldReceive('json')->once()->andReturn($data);
        return $response;
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
        $this->expectExceptionMessage('Неизвестный модуль: 999');
        $action->run(999, 'common', 'token');
    }

    public function test_requests_generation_and_polls_status(): void
    {
        $http = Mockery::mock(HttpTask::class);
        $fs = Mockery::mock(FilesystemTask::class);
        $poll = Mockery::mock(PollPartStatusTask::class);

        $http->shouldReceive('postWithToken')
            ->once()
            ->with('pull_updates/generatePartByNewCoreJson', 'token', ['part' => 'common'])
            ->andReturn($this->mockResponse([
                'operation_identity' => 'op-abc-123',
                'ttl' => 60,
            ]));

        $poll->shouldReceive('run')
            ->once()
            ->with('op-abc-123', 'token')
            ->andReturn(['status' => 'completed']);

        $checkResponse = Mockery::mock(Response::class);
        $checkResponse->shouldReceive('failed')->once()->andReturn(false);
        $http->shouldReceive('getWithToken')
            ->once()
            ->with('pull_updates/checkPartGenerationByNewCoreResultJson?operation_identity=op-abc-123&part=common', 'token')
            ->andReturn($checkResponse);

        // Create a minimal ZIP
        $tempZipDir = $this->tempDir . '/zip_source';
        File::makeDirectory($tempZipDir . '/common', 0755, true, true);
        file_put_contents($tempZipDir . '/common/index.html', '<html>new</html>');

        $zipPath = $this->tempDir . '/test_part.zip';
        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFile($tempZipDir . '/common/index.html', 'common/index.html');
        $zip->close();

        $zipContent = file_get_contents($zipPath);

        $http->shouldReceive('downloadToFile')
            ->once()
            ->andReturnUsing(function ($endpoint, $token, $filePath) use ($zipContent) {
                file_put_contents($filePath, $zipContent);
            });

        $fs->shouldReceive('validateFileTypes')
            ->once()
            ->andReturn([]);

        $fs->shouldReceive('atomicSwap')
            ->once()
            ->andReturn(true);

        $action = new UpdatePartAction($http, $fs, $poll, $this->tempDir, $this->tempDir, [
            1 => ['path' => 'sveden', 'allowed_folders' => ['common']],
        ]);

        $result = $action->run(1, 'common', 'token');

        $this->assertTrue($result['success']);
        $this->assertEquals("Раздел «common» обновлён.", $result['message']);
    }

    public function test_throws_on_generation_failure(): void
    {
        $http = Mockery::mock(HttpTask::class);
        $fs = Mockery::mock(FilesystemTask::class);
        $poll = Mockery::mock(PollPartStatusTask::class);

        $http->shouldReceive('postWithToken')
            ->once()
            ->andReturn($this->mockResponse([
                'message' => 'Generation not available',
            ]));

        $action = new UpdatePartAction($http, $fs, $poll, $this->tempDir, $this->tempDir, [
            1 => ['path' => 'sveden', 'allowed_folders' => ['common']],
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Не удалось запросить генерацию');
        $action->run(1, 'common', 'token');
    }

    public function test_throws_on_poll_failure(): void
    {
        $http = Mockery::mock(HttpTask::class);
        $fs = Mockery::mock(FilesystemTask::class);
        $poll = Mockery::mock(PollPartStatusTask::class);

        $http->shouldReceive('postWithToken')
            ->once()
            ->andReturn($this->mockResponse([
                'operation_identity' => 'op-abc-123',
                'ttl' => 60,
            ]));

        $poll->shouldReceive('run')
            ->once()
            ->andReturn(['status' => 'failed', 'error' => 'Server error']);

        $action = new UpdatePartAction($http, $fs, $poll, $this->tempDir, $this->tempDir, [
            1 => ['path' => 'sveden', 'allowed_folders' => ['common']],
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Ошибка генерации: Server error');
        $action->run(1, 'common', 'token');
    }
}
