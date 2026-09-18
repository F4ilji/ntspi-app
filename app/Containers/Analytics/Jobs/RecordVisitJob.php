<?php

namespace App\Containers\Analytics\Jobs;

use App\Containers\Analytics\Models\AnalyticsHit;
use App\Containers\Analytics\Models\AnalyticsSession;
use App\Containers\Analytics\Services\DeviceDetectorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecordVisitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 15;

    public function __construct(
        public array $data,
    ) {}

    public function handle(
        DeviceDetectorService $detector,
    ): void {
        $userAgent = $this->data['user_agent'] ?? '';

        if ($detector->isBot($userAgent)) {
            return;
        }

        $device = $detector->parse($userAgent);

        $utmParams = array_filter([
            'utm_source' => $this->data['utm_source'] ?? null,
            'utm_medium' => $this->data['utm_medium'] ?? null,
            'utm_campaign' => $this->data['utm_campaign'] ?? null,
        ], fn($v) => $v !== null);

        $session = (new AnalyticsSession())->firstOrCreateSession(
            visitorId: $this->data['visitor_id'],
            ip: $this->data['ip'] ?? '0.0.0.0',
            userId: $this->data['user_id'] ?? null,
            entryPage: $this->data['url'],
            utmParams: $utmParams,
        );

        if (empty($session->browser)) {
            $session->update([
                'browser' => $device['browser'],
                'os' => $device['os'],
                'device_type' => $device['type'],
            ]);
        }

        AnalyticsHit::create([
            'session_id' => $session->id,
            'user_id' => $this->data['user_id'] ?? null,
            'url' => $this->data['url'],
            'referrer' => $this->data['referrer'] ?? null,
            'title' => $this->data['title'] ?? null,
            'created_at' => now(),
        ]);
    }
}
