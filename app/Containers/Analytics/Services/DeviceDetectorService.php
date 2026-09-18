<?php

namespace App\Containers\Analytics\Services;

use WhichBrowser\Parser;

class DeviceDetectorService
{
    public function parse(string $userAgent): array
    {
        $parser = new Parser($userAgent);

        $browser = $parser->browser->name ?? null;
        $os = $parser->os->name ?? null;

        $deviceType = 'desktop';
        if ($parser->isType('tablet')) {
            $deviceType = 'tablet';
        } elseif ($parser->isType('mobile')) {
            $deviceType = 'mobile';
        }

        return [
            'browser' => $browser,
            'os' => $os,
            'type' => $deviceType,
        ];
    }

    public function isBot(string $userAgent): bool
    {
        $parser = new Parser($userAgent);
        return $parser->isType('bot');
    }
}
