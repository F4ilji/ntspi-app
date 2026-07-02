<?php

namespace App\Containers\Dashboard\UI\API\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Process;

class DeployController extends Controller
{
    private string $statusFile = '/tmp/deploy-status.json';
    private string $lockFile = '/tmp/deploy.lock';

    public function store(): JsonResponse
    {
        if (File::exists($this->lockFile)) {
            return response()->json([
                'error' => 'Deploy already in progress'
            ], 409);
        }

        Process::run('nohup php artisan deploy:run > /dev/null 2>&1 &');

        return response()->json(['message' => 'Deploy started']);
    }

    public function status(): JsonResponse
    {
        if (!File::exists($this->statusFile)) {
            return response()->json([
                'running' => false,
                'step' => 0,
                'total_steps' => 12,
                'current_step' => '',
                'logs' => [],
                'error' => null,
            ]);
        }

        $status = json_decode(File::get($this->statusFile), true);
        return response()->json($status);
    }
}
