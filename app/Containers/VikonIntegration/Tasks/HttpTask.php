<?php

namespace App\Containers\VikonIntegration\Tasks;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

class HttpTask
{
    public function __construct(
        private readonly string $apiDomain,
        private readonly string $authDomain,
        private readonly string $filemanagerDomain,
        private readonly int $timeout,
        private readonly int $retries,
    ) {}

    public function get(string $endpoint, array $params = [], string $service = 'api'): \Illuminate\Http\Client\Response
    {
        $url = $this->url($endpoint, $service);
        return $this->client()->get($url, $params);
    }

    public function post(string $endpoint, array $data = [], string $service = 'api'): \Illuminate\Http\Client\Response
    {
        $url = $this->url($endpoint, $service);
        return $this->client()->asForm()->post($url, $data);
    }

    public function getWithToken(string $endpoint, string $token, string $service = 'api'): \Illuminate\Http\Client\Response
    {
        $url = $this->url($endpoint, $service);
        return $this->client()
            ->withToken($token)
            ->get($url);
    }

    public function postWithToken(string $endpoint, string $token, array $data = [], string $service = 'api'): \Illuminate\Http\Client\Response
    {
        $url = $this->url($endpoint, $service);
        return $this->client()
            ->withToken($token)
            ->post($url, $data);
    }

    public function downloadWithToken(string $endpoint, string $token, string $service = 'api'): string
    {
        $url = $this->url($endpoint, $service);
        $response = $this->client()
            ->withToken($token)
            ->withHeaders(['Accept-Encoding' => 'zip, gzip'])
            ->get($url);

        if ($response->failed()) {
            throw new \RuntimeException('Download failed: HTTP ' . $response->status());
        }

        return $response->body();
    }

    private function client(): PendingRequest
    {
        $client = Http::timeout($this->timeout)
            ->retry($this->retries, 500)
            ->withHeaders(['Accept' => 'application/json']);

        if (config('vikon.domain_resolve')) {
            $client = $client->withOptions([
                'curl' => [
                    \CURLOPT_RESOLVE => [
                        'db-nica.ru:443:' . config('vikon.vikon_domain_resolve_ip'),
                        'file.db-nica.ru:443:' . config('vikon.fm_domain_resolve_ip'),
                    ],
                ],
            ]);
        }

        return $client;
    }

    private function url(string $endpoint, string $service): string
    {
        $base = match ($service) {
            'auth' => $this->authDomain,
            'filemanager' => $this->filemanagerDomain,
            default => $this->apiDomain,
        };
        return rtrim($base, '/') . '/' . ltrim($endpoint, '/');
    }
}
