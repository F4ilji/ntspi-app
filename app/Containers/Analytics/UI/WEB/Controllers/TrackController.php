<?php

namespace App\Containers\Analytics\UI\WEB\Controllers;

use App\Containers\Analytics\Jobs\RecordVisitJob;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrackController extends Controller
{
    public function store(Request $request): Response
    {
        $payload = [
            'visitor_id' => $request->input('visitor_id'),
            'url' => $request->input('url'),
            'referrer' => $request->input('referrer'),
            'title' => $request->input('title'),
            'screen_resolution' => $request->input('screen_resolution'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth('web')->id(),
        ];

        $url = parse_url($request->input('url', ''), PHP_URL_QUERY);
        if ($url) {
            parse_str($url, $queryParams);
            $payload['utm_source'] = $queryParams['utm_source'] ?? null;
            $payload['utm_medium'] = $queryParams['utm_medium'] ?? null;
            $payload['utm_campaign'] = $queryParams['utm_campaign'] ?? null;
        }

        RecordVisitJob::dispatch($payload)->onQueue('analytics');

        return response()->noContent();
    }
}
