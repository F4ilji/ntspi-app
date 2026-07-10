<?php

namespace Tests\Feature\Vicon;

use App\Containers\Dashboard\Models\IntegrationCredential;
use App\Services\Vicon\ViconApiConfig;
use App\Ship\Enums\CacheKeys;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ViconApiConfigTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function createCredential(array $payload, bool $active = true): void
    {
        IntegrationCredential::updateOrCreate(
            ['provider' => 'vikon_api'],
            ['payload' => $payload, 'is_active' => $active],
        );
    }

    public function test_token_returns_value_from_db(): void
    {
        $this->createCredential(['token' => 'test-jwt-token-123', 'api_url' => 'https://api.example.com']);

        $config = app(ViconApiConfig::class);

        $this->assertEquals('test-jwt-token-123', $config->token());
    }

    public function test_api_url_returns_value_from_db(): void
    {
        $this->createCredential(['token' => 'tok', 'api_url' => 'https://custom.api.com/v2']);

        $config = app(ViconApiConfig::class);

        $this->assertEquals('https://custom.api.com/v2', $config->apiUrl());
    }

    public function test_api_url_falls_back_when_not_in_payload(): void
    {
        $this->createCredential(['token' => 'tok']);

        $config = app(ViconApiConfig::class);

        $this->assertEquals('https://db-nica.ru/api/v1', $config->apiUrl());
    }

    public function test_token_throws_when_not_configured(): void
    {
        IntegrationCredential::where('provider', 'vikon_api')->delete();
        Cache::flush();

        $config = app(ViconApiConfig::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('VIKON API token not configured');

        $config->token();
    }

    public function test_token_throws_when_payload_empty(): void
    {
        $this->createCredential(['token' => '', 'api_url' => 'https://api.example.com']);

        $config = app(ViconApiConfig::class);

        $this->expectException(\RuntimeException::class);

        $config->token();
    }

    public function test_inactive_credential_is_ignored(): void
    {
        $this->createCredential(['token' => 'inactive-tok', 'api_url' => 'https://api.example.com'], active: false);

        $config = app(ViconApiConfig::class);

        $this->expectException(\RuntimeException::class);

        $config->token();
    }
}
