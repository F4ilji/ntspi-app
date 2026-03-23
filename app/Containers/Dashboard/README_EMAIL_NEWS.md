# 📧 Email News Fetching

Автоматическое получение новостей из Email и создание черновиков в системе.

## 📋 Описание

Функция позволяет автоматически:
- Подключаться к IMAP-серверу
- Получать непрочитанные письма от редактора
- Скачивать вложения (DOC, DOCX, изображения)
- Создавать новости через существующий `ProcessMixedFilesAction`
- Помечать обработанные письма как прочитанные

## 🏗 Архитектура (Porto)

```
app/Containers/Dashboard/
├── Actions/
│   └── FetchEmailNewsAction.php          # Оркестрация процесса
├── Tasks/
│   ├── ConnectToImapTask.php             # IMAP подключение
│   ├── FetchUnreadEmailsTask.php         # Получение писем
│   ├── FilterBySenderTask.php            # Фильтрация по отправителю
│   ├── DownloadAttachmentsTask.php       # Загрузка вложений
│   └── MarkEmailAsReadTask.php           # Пометка как прочитанное
├── Data/
│   └── EmailAttachmentData.php           # DTO для вложений
├── Exceptions/
│   └── EmailFetchException.php           # Исключения
├── Commands/
│   └── FetchEmailNewsCommand.php         # Artisan команда
└── Configs/
    └── email-news.php                    # Конфигурация
```

## 🔧 Установка

### 1. Пересобрать PHP-контейнер (требуется расширение IMAP)

```bash
docker-compose down
docker-compose build --no-cache ntspi-php
docker-compose up -d
```

### 2. Настроить переменные окружения

Скопируйте `.env.email-news.example` в `.env` и заполните:

```env
EMAIL_NEWS_ENABLED=true
EMAIL_NEWS_IMAP_HOST=imap.yandex.ru
EMAIL_NEWS_IMAP_PORT=993
EMAIL_NEWS_ENCRYPTION=ssl
EMAIL_NEWS_IMAP_USER=news@ntspi.ru
EMAIL_NEWS_IMAP_PASS=app_password
EMAIL_NEWS_SENDER_EMAIL=editor@example.com
EMAIL_NEWS_FOLDER=INBOX
```

### 3. Протестировать подключение

Запустите команду вручную:

```bash
docker exec -it ntspi-php php artisan email:fetch-news --log
```

## 📝 Использование

### Ручной запуск

```bash
# Обычный запуск
docker exec -it ntspi-php php artisan email:fetch-news

# С подробным логом
docker exec -it ntspi-php php artisan email:fetch-news --log

# Принудительный запуск (если отключено в конфиге)
docker exec -it ntspi-php php artisan email:fetch-news --force
```

### Автоматический запуск (Cron)

Команда автоматически добавлена в расписание Laravel Scheduler:
- **Частота:** каждые 5 минут
- **Защита от перекрытий:** `withoutOverlapping()`
- **Один сервер:** `onOneServer()`

Scheduler уже настроен в `app/Ship/Kernels/ConsoleKernel.php`.

Убедитесь, что Cron запущен в контейнере:

```bash
# Проверьте crontab
docker exec -it ntspi-php crontab -l

# Должно быть:
# * * * * * php /var/www/artisan schedule:run >> /dev/null 2>&1
```

## 🔐 Безопасность

### Фильтрация отправителей

Обрабатываются **только письма от редактора**, указанного в `EMAIL_NEWS_SENDER_EMAIL`.

Письма от других отправителей:
- ❌ Не обрабатываются
- ❌ Не скачиваются
- ✅ Логируются (если `EMAIL_NEWS_LOG_SKIPPED=true`)
- ✅ Остаются в папке (не помечаются прочитанными)

### Whitelist нескольких отправителей

В конфиге `app/Containers/Dashboard/Configs/email-news.php` можно указать несколько email:

```php
'allowed_senders' => [
    env('EMAIL_NEWS_SENDER_EMAIL'),
    env('EMAIL_NEWS_SENDER_EMAIL_2'),
    env('EMAIL_NEWS_SENDER_EMAIL_3'),
],
```

### App-Specific Password

**Рекомендуется** использовать специальный пароль приложения вместо основного пароля:

- **Yandex:** https://passport.yandex.ru/profile/passwords
- **Google:** https://myaccount.google.com/apppasswords
- **Mail.ru:** https://account.mail.ru/security

## 📊 Логирование

Логи доступны в `storage/logs/laravel.log`:

```log
[2024-01-15 10:30:00] local.INFO: [FetchEmailNewsAction] Начало получения новостей из Email
[2024-01-15 10:30:01] local.INFO: [ConnectToImapTask] Успешное подключение к IMAP
[2024-01-15 10:30:02] local.INFO: [FetchUnreadEmailsTask] Получены письма: count=3
[2024-01-15 10:30:02] local.INFO: [FilterBySenderTask] Фильтрация писем: total=3, filtered=2, skipped=1
[2024-01-15 10:30:05] local.INFO: [FetchEmailNewsAction:processEmail] Письмо успешно обработано: post_id=123
```

## ⚠️ Обработка ошибок

### Типичные ошибки

| Ошибка | Причина | Решение |
|--------|---------|---------|
| `Не удалось подключиться к IMAP-серверу` | Неправильный хост/порт/пароль | Проверьте `.env` настройки |
| `IMAP-папка не найдена` | Папка не существует | Проверьте имя папки в `EMAIL_NEWS_FOLDER` |
| `Письмо получено от неразрешённого отправителя` | Отправитель не в whitelist | Добавьте email в `allowed_senders` |
| `Нет DOC/DOCX файла для извлечения текста` | Во вложениях нет документа | Редактор должен прикрепить DOC/DOCX |

### Отладка

```bash
# Запуск с подробным выводом
docker exec -it ntspi-php php artisan email:fetch-news --log

# Просмотр последних логов
docker exec -it ntspi-php tail -f storage/logs/laravel.log

# Проверка расширения IMAP
docker exec -it ntspi-php php -m | grep imap
```

## 🔄 Поток данных

```
┌─────────────────────────────────────────────────────────┐
│  Cron (каждые 5 мин) → php artisan email:fetch-news    │
│                    ↓                                    │
│  FetchEmailNewsCommand                                  │
│                    ↓                                    │
│  FetchEmailNewsAction                                   │
│  ├─ ConnectToImapTask (IMAP-соединение)                │
│  ├─ FetchUnreadEmailsTask (получение писем)            │
│  ├─ FilterBySenderTask (проверка отправителя)          │
│  ├─ DownloadAttachmentsTask (скачивание вложений)      │
│  └─ ProcessMixedFilesAction (создание поста) ←───┐    │
│                    ↓                              │    │
│  MarkEmailAsReadTask (пометка как прочитанное)    │    │
└───────────────────────────────────────────────────┼────┘
                                                    │
                    ┌───────────────────────────────┘
                    ↓
         Существующий процесс создания новости
         (AI-распознавание, сжатие изображений, и т.д.)
```

## 🧪 Тестирование

### Отправка тестового письма

1. Отправьте письмо с `EMAIL_NEWS_SENDER_EMAIL` на `EMAIL_NEWS_IMAP_USER`
2. Прикрепите DOC/DOCX файл (текст новости)
3. Прикрепите изображения (опционально)
4. Запустите команду:

```bash
docker exec -it ntspi-php php artisan email:fetch-news --log
```

5. Проверьте создание новости в Dashboard

### Мок-тестирование (для разработчиков)

```php
// В Unit-тестах можно моковать IMAP
$this->mock(ClientManager::class, function ($mock) {
    $mock->shouldReceive('account')
         ->with('email_news')
         ->andReturn($client);
});
```

## 📚 Ссылки

- [Webklex PHP-IMAP Documentation](https://github.com/Webklex/php-imap)
- [Laravel Scheduler](https://laravel.com/docs/10.x/scheduling)
- [Porto Architecture](https://github.com/AlxDorosenco/Porto)
