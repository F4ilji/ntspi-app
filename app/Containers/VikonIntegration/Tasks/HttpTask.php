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
    ) {
        if (php_sapi_name() !== 'cli') {
            set_time_limit(600);
        }
    }

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
            ->asForm()
            ->post($url, $data);
    }

    public function downloadWithToken(string $endpoint, string $token, string $service = 'api'): string
    {
        $url = $this->url($endpoint, $service);
        $response = $this->client()
            ->withToken($token)
            ->withHeaders(['Accept-Encoding' => 'zip, gzip'])
            ->timeout(300)
            ->get($url);

        if ($response->failed()) {
            throw new \RuntimeException('Download failed: HTTP ' . $response->status());
        }

        return $response->body();
    }

    /**
     * Download a file directly to disk using cURL streaming (avoids memory limit for large files).
     */
    public function downloadToFile(string $endpoint, string $token, string $filePath, string $service = 'api'): void
    {
        $url = $this->url($endpoint, $service);

        $headers = [
            'Authorization: Bearer ' . $token,
            'Accept-Encoding: zip, gzip',
        ];

        if (config('vikon.domain_resolve')) {
            $resolve = [
                'db-nica.ru:443:' . config('vikon.vikon_domain_resolve_ip'),
                'file.db-nica.ru:443:' . config('vikon.fm_domain_resolve_ip'),
            ];
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_CONNECTTIMEOUT => 30,
        ]);

        if (!empty($resolve)) {
            curl_setopt($ch, CURLOPT_RESOLVE, $resolve);
        }

        $fp = fopen($filePath, 'w');
        if (!$fp) {
            curl_close($ch);
            throw new \RuntimeException("Cannot open file for writing: {$filePath}");
        }

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec($ch);

        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);

        if ($error) {
            @unlink($filePath);
            throw new \RuntimeException('cURL download error: ' . $error);
        }

        if ($httpCode !== 200) {
            @unlink($filePath);
            throw new \RuntimeException("Download failed with HTTP {$httpCode}");
        }
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
