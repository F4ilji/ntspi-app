<?php

namespace App\Containers\VikonIntegration\Tests\Unit;

use App\Containers\VikonIntegration\Tasks\PollPartStatusTask;
use App\Containers\VikonIntegration\Tasks\HttpTask;
use App\Ship\Tests\TestCase;
use Illuminate\Http\Client\Response;
use Mockery;

class PollPartStatusTaskTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_completed_when_status_is_1(): void
    {
        $http = Mockery::mock(HttpTask::class);
        $response = Mockery::mock(Response::class);
        $response->shouldReceive('json')->once()->andReturn(['status' => 1]);

        $http->shouldReceive('getWithToken')
            ->once()
            ->with(
                Mockery::on(fn ($endpoint) => str_contains($endpoint, 'getStatusPartGeneration')),
                'test-token'
            )
            ->andReturn($response);

        $task = new PollPartStatusTask($http, 3, 50);
        $result = $task->run('op-123', 'test-token');

        $this->assertEquals('completed', $result['status']);
    }

    public function test_returns_failed_when_status_is_negative_1(): void
    {
        $http = Mockery::mock(HttpTask::class);
        $response = Mockery::mock(Response::class);
        $response->shouldReceive('json')->once()->andReturn([
            'status' => -1,
            'message' => 'Generation error',
        ]);

        $http->shouldReceive('getWithToken')
            ->once()
            ->andReturn($response);

        $task = new PollPartStatusTask($http, 3, 50);
        $result = $task->run('op-123', 'test-token');

        $this->assertEquals('failed', $result['status']);
        $this->assertEquals('Generation error', $result['error']);
    }

    public function test_returns_timeout_after_max_attempts(): void
    {
        $http = Mockery::mock(HttpTask::class);
        $response = Mockery::mock(Response::class);
        $response->shouldReceive('json')->andReturn(['status' => 0]);

        $http->shouldReceive('getWithToken')
            ->times(3)
            ->andReturn($response);

        $task = new PollPartStatusTask($http, 0, 3);
        $result = $task->run('op-123', 'test-token');

        $this->assertEquals('timeout', $result['status']);
    }
}
