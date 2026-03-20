<?php

namespace App\Services\VK;

use App\Services\VK\Album\VkAlbumService;
use App\Services\VK\Wall\VkWallService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use VK\Client\VKApiClient;

class VkAuthService
{

    public function getToken()
    {
        try {
            // Получаем последний токен
            $token = DB::table('vk_tokens')->latest()->first();

            // Проверяем, существует ли токен и является ли он валидным
            if ($token && !$this->isTokenValid($token)) {
                // Если токен не валиден, обновляем его
                return $this->refresh();
            }
            return $token;
        } catch (\Exception $e) {
            // Обработка ошибок, например, логирование
            Log::error('Ошибка при получении токена: ' . $e->getMessage());
            return null; // Или выбросьте исключение, если это необходимо
        }
    }

    public function redirectToProvider()
    {
        $state = bin2hex(random_bytes(16)); // Генерирует случайную строку состояния (state) для защиты от CSRF-атак.
        session(['vk_state' => $state]); // Сохраняет строку состояния в сессии под ключом 'vk_state'.

        $code_verifier = $this->generateCodeVerifier(); // Создает случайную строку code_verifier для протокола PKCE.
        $code_challenge = $this->generateCodeChallenge($code_verifier); // Генерирует code_challenge на основе code_verifier (обычно хеш SHA-256).

        session(['vk_code_verifier' => $code_verifier]); // Сохраняет code_verifier в сессии под ключом 'vk_code_verifier'.

        $url = 'https://id.vk.ru/authorize?' . http_build_query([ // Формирует URL для перенаправления на страницу авторизации VK ID с параметрами:
                'response_type' => 'code', // Указывает, что нужен код авторизации.
                'client_id' => config('services.vk.app_id'), // ID приложения VK из переменных окружения.
                'redirect_uri' => config('services.vk.redirect_uri'), // URI для перенаправления после авторизации.
                'state' => $state, // Передает строку состояния для проверки.
                'scope' => 'photos wall video docs', // Запрашивает доступ к фото, стене, видео и документам.
                'code_challenge' => $code_challenge, // Передает code_challenge для PKCE.
                'code_challenge_method' => 's256', // Указывает метод генерации code_challenge (SHA-256).
            ]);

        return redirect($url); // Перенаправляет пользователя на сформированный URL для авторизации.
    }

    public function handleProviderCallback(Request $request)
    {
        $this->validateState($request);
        $codeVerifier = session('vk_code_verifier');

        $tokenData = $this->exchangeCodeForTokens($request, $codeVerifier);


        if (isset($tokenData->error)) {
            return response()->json(['error' => $tokenData->error_description], 400);
        }

        return $this->storeTokenData($tokenData, $request);
    }

    public function refresh()
    {
        $token = DB::table('vk_tokens')->latest()->first();
        $newTokenData = $this->refreshToken($token->refresh_token, bin2hex(random_bytes(16)), $token->device_id);
        return $this->storeRefreshTokenData($newTokenData['data']);
    }

    public function logout()
    {
        session()->forget('vk_state');
        session()->forget('vk_code_verifier');

        return redirect('/'); // Перенаправление на главную страницу
    }

    private function exchangeCodeForTokens(Request $request, $codeVerifier)
    {
        return Http::asForm()->post('https://id.vk.ru/oauth2/auth', [
            'grant_type' => 'authorization_code',
            'code_verifier' => $codeVerifier,
            'redirect_uri' => config('services.vk.'),
            'code' => $request->code,
            'client_id' => config('services.vk.app_id'),
            'device_id' => $request->device_id,
            'state' => $request->state,
            'scope' => 'photos wall video docs',
        ])->object();
    }

    private function refreshToken($refresh_token, $state, $device_id)
    {
        $response = Http::asForm()->post('https://id.vk.ru/oauth2/auth', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
            'client_id' => config('services.vk.app_id'),
            'device_id' => $device_id,
            'state' => $state,
            'scope' => 'photos wall video docs',
        ]);

        if ($response->failed()) {
            // Обработка ошибок
            return [
                'success' => false,
                'error' => $response->json(), // Возвращаем детали ошибки
            ];
        }

        return [
            'success' => true,
            'data' => $response->object(), // Успешный ответ
        ];
    }

    private function storeTokenData($tokenData, Request $request)
    {
        try {
            DB::table('vk_tokens')->updateOrInsert(
                ['user_id' => $tokenData->user_id],
                [
                    'user_id' => $tokenData->user_id,
                    'access_token' => $tokenData->access_token,
                    'refresh_token' => $tokenData->refresh_token,
                    'id_token' => $tokenData->id_token,
                    'state' => $tokenData->state,
                    'scope' => $tokenData->scope,
                    'device_id' => $request->device_id,
                    'token_expire' => Carbon::createFromTimestamp(Carbon::now()->timestamp + $tokenData->expires_in),
                    'updated_at' => now(),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении или вставке токена: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при обновлении или создания токена.',
                'error_message' => $e->getMessage()
            ], 500);
        }

        return redirect('/'); // Перенаправление после успешной авторизации
    }

    private function storeRefreshTokenData($tokenData)
    {
        try {
            // Обновляем запись и получаем количество затронутых строк
            $updatedRows = DB::table('vk_tokens')->where('user_id', $tokenData->user_id)->update(
                [
                    'access_token' => $tokenData->access_token,
                    'refresh_token' => $tokenData->refresh_token,
                    'state' => $tokenData->state,
                    'scope' => $tokenData->scope,
                    'token_expire' => Carbon::createFromTimestamp(Carbon::now()->timestamp + $tokenData->expires_in),
                    'updated_at' => now(),
                ]
            );

            // Если обновление прошло успешно, получаем обновленную запись
            if ($updatedRows > 0) {
                return DB::table('vk_tokens')->where('user_id', $tokenData->user_id)->first();
            }

            // Если запись не найдена, можно вернуть null или выбросить исключение
            return response()->json([
                'success' => false,
                'message' => 'Запись не найдена для обновления.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении токена: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при обновлении токена.',
                'error_message' => $e->getMessage()
            ], 500);
        }
    }
    private function generateCodeVerifier($length = 128)
    {
        return bin2hex(random_bytes($length / 2));
    }

    private function generateCodeChallenge($code_verifier)
    {
        return rtrim(strtr(base64_encode(hash('sha256', $code_verifier, true)), '+/', '-_'), '=');
    }

    private function validateState(Request $request)
    {
        if ($request->input('state') !== session('vk_state')) {
            abort(403, 'Invalid state');
        }
    }

    private function isTokenValid($token)
    {
        if (Carbon::now() > $token->token_expire) {
            return false;
        }
        return true;
    }


}