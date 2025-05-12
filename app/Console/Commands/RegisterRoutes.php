<?php

namespace App\Console\Commands;

use App\Containers\AppStructure\Models\Page;
use Illuminate\Support\Facades\Route;
use Illuminate\Console\Command;



class RegisterRoutes extends Command
{
    protected $signature = 'routes:register';
    protected $description = 'Register application routes in the database';

    public function handle()
    {
        $routes = Route::getRoutes();

        foreach ($routes as $route) {
            // Проверяем, существует ли маршрут в базе данных
            if (!Page::where('path', '=', $route->uri)->where('is_registered', '=', true)->exists()) {
                // Если не существует, создаем новую запись
                Page::create([
                    'path' => $route->uri,
                    'is_registered' => true,
                    'is_url' => false,
                    'searchable' => false,
                    'code' => 200,
                ]);

                $this->info("Маршрут зарегистрирован: " . $route->uri);
            } else {
                $this->info("Маршрут уже существует: " . $route->uri);
            }
        }

        $this->info('Все маршруты успешно проверены.');
    }
}


