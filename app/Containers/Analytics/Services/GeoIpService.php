<?php

namespace App\Containers\Analytics\Services;

use App\Containers\Analytics\Models\AnalyticsGeoCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoIpService
{
    private const CACHE_TTL_DAYS = 7;

    public function locate(string $ip): ?array
    {
        $ipHash = sha1($ip);

        $cached = AnalyticsGeoCache::where('ip_hash', $ipHash)
            ->where('cached_at', '>', now()->subDays(self::CACHE_TTL_DAYS))
            ->first();

        if ($cached) {
            return [
                'country' => $cached->country,
                'city' => $cached->city,
            ];
        }

        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}", [
                'fields' => 'country,city',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                AnalyticsGeoCache::updateOrCreate(
                    ['ip_hash' => $ipHash],
                    [
                        'country' => $data['country'] ?? null,
                        'city' => $data['city'] ?? null,
                        'cached_at' => now(),
                    ]
                );

                return [
                    'country' => $data['country'] ?? null,
                    'city' => $data['city'] ?? null,
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('GeoIP lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);
        }

        return null;
    }
}
