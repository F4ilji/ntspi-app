<?php

namespace App\Containers\VikonIntegration\Tasks;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

/**
 * Task: Makes secure HTTP requests to Vikon API (db-nica.ru)
 *
 * SSL verification is ENABLED by default (unlike old vikon_core).
 * Uses Laravel Http facade with proper timeout and retry settings.
 */
class CallVikonApiTask
{
    public function __construct(
        private readonly string $apiDomain,
        private readonly string $authDomain,
        private readonly string $filemanagerDomain,
        private readonly int $timeout,
        private readonly int $retries,
    ) {}

    /**
     * GET request to Vikon API
     */
    public function get(string $endpoint, array $headers = [], string $service = 'api'): \Illuminate\Http\Client\Response
    {
        $baseUrl = $this->resolveBaseUrl($service);
        $url = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');

        return $this->makeRequest('get', $url, [], $headers);
    }

    /**
     * POST request to Vikon API
     */
    public function post(string $endpoint, array $data = [], array $headers = [], string $service = 'api'): \Illuminate\Http\Client\Response
    {
        $baseUrl = $this->resolveBaseUrl($service);
        $url = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');

        return $this->makeRequest('post', $url, $data, $headers);
    }

    /**
     * GET request with Bearer token authorization
     */
    public function getWithToken(string $endpoint, string $token, string $service = 'api'): \Illuminate\Http\Client\Response
    {
        $baseUrl = $this->resolveBaseUrl($service);
        $url = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');

        return $this->makeRequest('get', $url, [], [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]);
    }

    /**
     * POST request with Bearer token authorization
     */
    public function postWithToken(string $endpoint, string $token, array $data = [], string $service = 'api'): \Illuminate\Http\Client\Response
    {
        $baseUrl = $this->resolveBaseUrl($service);
        $url = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');

        return $this->makeRequest('post', $url, $data, [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]);
    }

    /**
     * Download binary content (ZIP archive) with Bearer token
     */
    public function downloadWithToken(string $endpoint, string $token, string $service = 'api'): string
    {
        $baseUrl = $this->resolveBaseUrl($service);
        $url = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept-Encoding' => 'zip, gzip',
            'Accept' => 'application/json',
        ])
            ->timeout($this->timeout)
            ->retry($this->retries, 1000, function ($exception, $response) {
                if ($response && $response->successful()) {
                    return false;
                }
                return true;
            })
            ->get($url);

        if ($response->failed()) {
            throw new \RuntimeException(
                'Vikon API error: ' . $this->extractErrorMessage($response) . ' (HTTP ' . $response->status() . ')'
            );
        }

        return $response->body();
    }

    /**
     * Resolve base URL by service type
     */
    private function resolveBaseUrl(string $service): string
    {
        return match ($service) {
            'auth' => $this->authDomain,
            'filemanager' => $this->filemanagerDomain,
            default => $this->apiDomain,
        };
    }

    /**
     * Make HTTP request with common settings
     *
     * @throws ConnectionException
     * @throws \RuntimeException
     */
    private function makeRequest(string $method, string $url, array $data, array $headers): \Illuminate\Http\Client\Response
    {
        $http = Http::withHeaders(array_merge([
            'Accept' => 'application/json',
        ], $headers))
            ->timeout($this->timeout)
            ->retry($this->retries, 1000, function ($exception, $response) {
                if ($response && $response->successful()) {
                    return false;
                }
                return true;
            });

        $response = match ($method) {
            'get' => $data ? $http->get($url, $data) : $http->get($url),
            'post' => $http->post($url, $data),
        };

        if ($response->failed()) {
            throw new \RuntimeException(
                'Vikon API error: ' . $this->extractErrorMessage($response) . ' (HTTP ' . $response->status() . ')'
            );
        }

        return $response;
    }

    /**
     * Extract error message from API response
     */
    private function extractErrorMessage(\Illuminate\Http\Client\Response $response): string
    {
        $body = $response->json();

        if (isset($body['message'])) {
            return $body['message'];
        }

        if (isset($body['error'])) {
            return $body['error'];
        }

        if (isset($body['messages']) && is_array($body['messages'])) {
            return implode('; ', $body['messages']);
        }

        return 'Unknown error';
    }
}
