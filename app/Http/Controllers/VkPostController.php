<?php

namespace App\Http\Controllers;

use App\Services\VK\VkService;
use Illuminate\Http\Request;
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

    public function index()
    {
        $oauth = new VKOAuth();
        $client_id = 52468445;
        $redirect_uri = 'https://crawdad-fresh-bream.ngrok-free.app/';
        $display = VKOAuthDisplay::PAGE;
        $scope = array(VKOAuthUserScope::WALL, VKOAuthUserScope::PHOTOS);
        $state = 'dJZ3N05uZc9jpcEgxD6y';
        $groups_ids = array(227826614);

        $browser_url = $oauth->getAuthorizeUrl(VKOAuthResponseType::TOKEN, $client_id, $redirect_uri, $display, $scope, $state, $groups_ids);
        return redirect($browser_url);

    }

    public function wall()
    {
        return $this->vkService->createAlbum('Тестовый альбом');
    }




}
