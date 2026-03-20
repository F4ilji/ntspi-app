<?php

namespace App\Containers\AppStructure\Tasks;

use App\Containers\AppStructure\Models\Page;
use Illuminate\Support\Facades\Route;

class RegisterApplicationRoutesTask
{
    public function run(): void
    {
        $routes = Route::getRoutes();

        foreach ($routes as $route) {
            if (!Page::where('path', '=', $route->uri)->where('is_registered', '=', true)->exists()) {
                Page::create([
                    'path' => $route->uri,
                    'is_registered' => true,
                    'is_url' => false,
                    'searchable' => false,
                    'code' => 200,
                ]);
            }
        }
    }
}
