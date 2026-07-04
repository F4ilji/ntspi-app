<?php

namespace App\Containers\VikonIntegration\Actions\Auth;

use App\Containers\VikonIntegration\Tasks\HttpTask;

class AuthenticateAction
{
    public function __construct(
        private readonly HttpTask $http,
        private readonly string $clientId,
        private readonly string $clientSecret,
    ) {}

    /**
     * @return array{access_token: string, refresh_token: string}
     */
    public function run(string $code, string $redirectUri): array
    {
        $response = $this->http->post('oauth2/authorize/token', [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ], 'auth');

        $body = $response->json();

        if (!isset($body['access_token'], $body['refresh_token'])) {
            throw new \RuntimeException('Auth failed: ' . ($body['message'] ?? 'Unknown error'));
        }

        return [
            'access_token' => $body['access_token'],
            'refresh_token' => $body['refresh_token'],
        ];
    }
}
