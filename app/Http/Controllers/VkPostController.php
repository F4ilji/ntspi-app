<?php

namespace App\Http\Controllers;

use App\Jobs\CreateVkPost;
use App\Services\VK\VkAuthService;
use App\Services\VK\VkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Symfony\Component\Finder\Finder;
use VK\Client\VKApiClient;
use VK\OAuth\Scopes\VKOAuthUserScope;
use VK\OAuth\VKOAuth;
use VK\OAuth\VKOAuthDisplay;
use VK\OAuth\VKOAuthResponseType;

class VkPostController extends Controller
{
    private string $wall_token;

    private VkService $vkService;


    public function __construct()
    {
        $this->vkService = new VkService(new VKApiClient());
        $this->wall_token = env('WALL_ACCESS_VK_TOKEN');
    }

    public function wall()
    {
        dd($this->vkService->getPostById(39));
    }


    public function getImages()
    {
        // Укажите путь к директории
        $directory = 'public/images';

        // Получаем все файлы из директории
        $files = Storage::files($directory);


        // Фильтруем только изображения (например, jpg, png)
        $images = array_filter($files, function ($file) {
            return in_array(pathinfo($file, PATHINFO_EXTENSION), ['webp', 'jpeg', 'png', 'gif']);
        });

        // Формируем полный URL для каждого изображения
        $imageUrls = array_map(function ($file) {
            return url(Storage::url($file)); // Добавляем домен
        }, $images);

        return $imageUrls; // Возвращаем массив с полными URL изображений
    }





}
