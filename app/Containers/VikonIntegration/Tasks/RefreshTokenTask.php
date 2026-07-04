<?php

namespace App\Containers\VikonIntegration\Tasks;

class RefreshTokenTask
{
    public function __construct(
        private readonly HttpTask $http,
        private readonly string $clientId,
        private readonly string $clientSecret,
    ) {}

    /**
     * @return array{access_token: string, refresh_token: string}
     */
    public function run(string $refreshToken): array
    {
        $response = $this->http->post('oauth2/RefreshToken', [
            'refresh_token' => $refreshToken,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token',
        ]);

        $body = $response->json();

        if (!isset($body['access_token'], $body['refresh_token'])) {
            throw new \RuntimeException('Token refresh failed: ' . ($body['message'] ?? 'Unknown'));
        }

        return [
            'access_token' => $body['access_token'],
            'refresh_token' => $body['refresh_token'],
        ];
    }
}
