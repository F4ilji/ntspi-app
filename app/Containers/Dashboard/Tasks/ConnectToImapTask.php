<?php

namespace App\Containers\Dashboard\Tasks;

use App\Containers\Dashboard\Exceptions\EmailFetchException;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Folder;
use Illuminate\Support\Facades\Log;

/**
 * Подключение к IMAP-серверу
 */
class ConnectToImapTask
{
    /**
     * Подключиться к IMAP-серверу
     *
     * @param string|null $accountName Имя аккаунта из config/imap.php
     * @return Client IMAP клиент
     * @throws EmailFetchException
     */
    public function run(?string $accountName = null): Client
    {
        $accountName = $accountName ?? config('email-news.imap_account', 'email_news');
        
        // Получаем конфиг для webklex/php-imap
        $imapConfig = config('imap');

        Log::info('[ConnectToImapTask] Попытка подключения к IMAP', [
            'account' => $accountName,
            'host' => $imapConfig['accounts'][$accountName]['host'] ?? 'unknown',
        ]);

        try {
            // Создаём ClientManager с явным конфигом
            $clientManager = new ClientManager($imapConfig);
            $client = $clientManager->account($accountName);
            $client->connect();

            Log::info('[ConnectToImapTask] Успешное подключение к IMAP', [
                'account' => $accountName,
            ]);

            return $client;
        } catch (\Exception $e) {
            Log::error('[ConnectToImapTask] Ошибка подключения к IMAP', [
                'account' => $accountName,
                'error' => $e->getMessage(),
            ]);

            throw EmailFetchException::connectionFailed($e->getMessage());
        }
    }

    /**
     * Получить папку
     *
     * @param Client $client IMAP клиент
     * @param string $folderName Имя папки
     * @return Folder
     * @throws EmailFetchException
     */
    public function getFolder(Client $client, string $folderName): Folder
    {
        Log::info('[ConnectToImapTask] Получение папки', [
            'folder' => $folderName,
        ]);

        try {
            // Пробуем получить папку напрямую
            $folder = $client->getFolder($folderName);
            
            // Если не получилось, ищем в списке папок
            if (!$folder) {
                $folders = $client->getFolders();
                foreach ($folders as $f) {
                    if ($f->name === $folderName || $f->path === $folderName) {
                        $folder = $f;
                        break;
                    }
                }
            }

            if (!$folder) {
                throw EmailFetchException::folderNotFound($folderName);
            }

            Log::info('[ConnectToImapTask] Папка получена успешно', [
                'folder' => $folderName,
                'fullName' => $folder->full_name ?? $folder->name ?? $folderName,
            ]);

            return $folder;
        } catch (EmailFetchException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('[ConnectToImapTask] Ошибка получения папки', [
                'folder' => $folderName,
                'error' => $e->getMessage(),
            ]);

            throw EmailFetchException::folderNotFound($folderName);
        }
    }
}
