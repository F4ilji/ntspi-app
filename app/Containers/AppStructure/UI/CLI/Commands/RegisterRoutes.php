<?php

namespace App\Containers\AppStructure\UI\CLI\Commands;

use App\Ship\Abstracts\Commands\ConsoleCommand as AbstractConsoleCommand;
use App\Containers\AppStructure\Models\Page;
use Illuminate\Support\Facades\Route;
class RegisterRoutes extends AbstractConsoleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routes:register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register application routes in the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
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
