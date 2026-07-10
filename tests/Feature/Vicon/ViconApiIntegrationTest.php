<?php

namespace Tests\Feature\Vicon;

use App\Containers\Dashboard\Models\IntegrationCredential;
use App\Services\Vicon\DirectionStudy\DirectionStudyService;
use App\Services\Vicon\EducationalProgram\EducationalProgramService;
use App\Services\Vicon\ViconApiConfig;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ViconApiIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $token = env('VIKON_API_TOKEN');
        if ($token && !IntegrationCredential::where('provider', 'vikon_api')->exists()) {
            IntegrationCredential::create([
                'provider' => 'vikon_api',
                'payload' => ['token' => $token, 'api_url' => 'https://db-nica.ru/api/v1'],
                'is_active' => true,
            ]);
        }
    }

    private function skipIfNoToken(): void
    {
        try {
            app(ViconApiConfig::class)->token();
        } catch (\RuntimeException) {
            $this->markTestSkipped('VIKON_API_TOKEN not set — skipping live API test');
        }
    }

    public function test_get_naprs_returns_data(): void
    {
        $this->skipIfNoToken();

        $result = app(DirectionStudyService::class)->getNaprs(1);

        $this->assertIsObject($result);
        $this->assertObjectHasProperty('rows', $result);
        $this->assertNotEmpty($result->rows);
    }

    public function test_get_programs_returns_data(): void
    {
        $this->skipIfNoToken();

        $result = app(DirectionStudyService::class)->getPrograms(1);

        $this->assertIsObject($result);
        $this->assertObjectHasProperty('rows', $result);
        $this->assertNotEmpty($result->rows);
    }

    public function test_get_programs_via_educational_service(): void
    {
        $this->skipIfNoToken();

        $result = app(EducationalProgramService::class)->getPrograms(1);

        $this->assertIsObject($result);
        $this->assertObjectHasProperty('rows', $result);
        $this->assertNotEmpty($result->rows);
    }

    public function test_get_program_by_uuid(): void
    {
        $this->skipIfNoToken();

        $programs = app(DirectionStudyService::class)->getPrograms(1);
        if (empty($programs->rows)) {
            $this->markTestSkipped('No programs for level 1');
        }

        $uuid = $programs->rows[0]->uuid;
        $result = app(DirectionStudyService::class)->getProgram($uuid);

        $this->assertIsObject($result);
        $this->assertEquals($uuid, $result->uuid);
    }

    public function test_api_rejects_invalid_token(): void
    {
        Http::fake(['*' => Http::response(['message' => 'Unauthorized'], 401)]);

        $this->mock(ViconApiConfig::class, function ($mock) {
            $mock->shouldReceive('token')->andReturn('invalid-token');
            $mock->shouldReceive('apiUrl')->andReturn('https://db-nica.ru/api/v1');
        });

        $this->expectException(\Exception::class);
        app(DirectionStudyService::class)->getNaprs(1);
    }
}
