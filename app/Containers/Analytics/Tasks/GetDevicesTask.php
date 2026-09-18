<?php

namespace App\Containers\Analytics\Tasks;

use App\Containers\Analytics\Models\AnalyticsSession;
use Illuminate\Support\Carbon;

class GetDevicesTask
{
    public function run(int $days = 30): array
    {
        $from = Carbon::now()->subDays($days)->startOfDay();

        $devices = AnalyticsSession::where('started_at', '>=', $from)
            ->selectRaw('
                device_type,
                COUNT(*) as count
            ')
            ->groupBy('device_type')
            ->get()
            ->pluck('count', 'device_type')
            ->toArray();

        $browsers = AnalyticsSession::where('started_at', '>=', $from)
            ->whereNotNull('browser')
            ->selectRaw('
                browser,
                COUNT(*) as count
            ')
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->pluck('count', 'browser')
            ->toArray();

        $oses = AnalyticsSession::where('started_at', '>=', $from)
            ->whereNotNull('os')
            ->selectRaw('
                os,
                COUNT(*) as count
            ')
            ->groupBy('os')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->pluck('count', 'os')
            ->toArray();

        return [
            'devices' => $devices,
            'browsers' => $browsers,
            'oses' => $oses,
        ];
    }
}
