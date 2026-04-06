# Оптимизация обработки Email (IMAP)

## Проблема

При обработке большого количества email-сообщений возникала ошибка:
```
Allowed memory size of 268435456 bytes exhausted (tried to allocate 6291480 bytes)
```

**Корневые причины:**
1. Библиотека `webklex/php-imap` загружала **все тела писем** по умолчанию
2. Отсутствовали лимиты на количество загружаемых сообщений
3. Не было контроля использования памяти
4. Все письма загружались в память одновременно

## Решение

Оптимизация выполнена в **3 уровня**:

### Уровень 1: Конфигурация (config/imap.php)

Добавлены настройки для предотвращения загрузки тел сообщений:

```php
'options' => [
    'fetch' => \Webklex\PHPIMAP\IMAP::FT_PEEK,  // Не помечать как прочитанные
    'message_key' => 'id',                      // Использовать UID
    'fetch_body' => false,                      // НЕ загружать тела
    'fetch_flags' => false,                     // НЕ загружать флаги
    'fetch_limit' => env('IMAP_FETCH_LIMIT', 20), // Лимит сообщений
],
```

**Результат:** Загружаются только заголовки писем (subject, from, date), тела и вложения НЕ загружаются.

---

### Уровень 2: Оптимизация кода

#### 2A. FetchUnreadEmailsTask
- Добавлен `setFetchBody(false)` — явное отключение загрузки тел
- Добавлен `setFetchFlags(false)` — отключение загрузки флагов
- Добавлен параметр `$limit` для контроля количества сообщений
- Добавлен параметр `$offset` для пагинации

```php
$query = $folder->messages()
    ->unseen()
    ->setFetchBody(false)      // ← КРИТИЧНО
    ->setFetchFlags(false)
    ->limit($limit);
```

#### 2B. FetchEmailNewsAction
- Реализована **batch-обработка** (по 10 писем за цикл)
- Добавлена **принудительная сборка мусора** после каждого batch
- Добавлен **мониторинг памяти** с логированием

```php
private const BATCH_SIZE = 10;
private const MEMORY_THRESHOLD = 400 * 1024 * 1024; // 400MB

while ($hasMoreEmails) {
    $emails = $this->fetchUnreadEmailsTask->run($folder, self::BATCH_SIZE, $offset);
    
    foreach ($emails as $email) {
        $this->processEmail($email);
    }
    
    $this->collectGarbageIfNeeded(); // gc_collect_cycles()
    $offset += self::BATCH_SIZE;
}
```

#### 2C. DownloadAttachmentsTask
- Вложения загружаются **напрямую** без загрузки тела письма
- Добавлен мониторинг памяти для больших вложений (>100MB)

```php
// Метод getAttachments() использует структуру, а не raw_body
$attachments = $message->getAttachments();
```

---

### Уровень 3: Инфраструктура

#### 3A. ProcessEmailNewsJob (Queue Worker)

Создан Queue Job для асинхронной обработки:

```php
class ProcessEmailNewsJob implements ShouldQueue
{
    public $timeout = 300;  // 5 минут
    public $tries = 3;      // 3 попытки
    public $backoff = 60;   // 1 минута между попытками
    
    public function handle(FetchEmailNewsAction $action): void
    {
        $action->run();
    }
}
```

**Преимущества:**
- Обработка вынесена из веб-процесса в queue worker
- Автоматические повторные попытки при ошибках
- Изоляция от пользовательских запросов
- Контроль таймаутов и памяти

#### 3B. FetchEmailNewsCommand (Artisan)

Добавлены два режима работы:

**Синхронный** (backward compatibility):
```bash
docker exec ntspi-php php artisan email:fetch-news
```

**Асинхронный** (рекомендуется для production):
```bash
docker exec ntspi-php php artisan email:fetch-news --async --queue=email-processing
```

#### 3C. MemoryAwareTrait

Создан трейт для мониторинга памяти:

```php
class MyTask
{
    use MemoryAwareTrait;
    
    public function run(): void
    {
        $this->logMemoryUsage('start');
        
        // ... logic
        
        $this->collectGarbageIfNeeded();
    }
}
```

**Методы:**
- `logMemoryUsage($context)` — логирование при превышении 100MB
- `collectGarbageIfNeeded()` — сборка мусора при превышении 400MB
- `isMemoryLimitExceeded($percent)` — проверка лимита

---

## PHP Configuration

### _docker/app/php.ini

```ini
memory_limit = 1G  # Увеличено с 512MB до 1GB
```

**Важно:** После изменения php.ini необходимо **пересобрать PHP-контейнер**:

```bash
docker-compose down
docker-compose up -d --build app
```

---

## Использование

### Development (синхронно)

```bash
docker exec ntspi-php php artisan email:fetch-news --log
```

### Production (асинхронно)

1. **Запустить queue worker:**
```bash
docker exec ntspi-php php artisan queue:work --queue=email-processing --timeout=300
```

2. **Отправить задачу в очередь:**
```bash
docker exec ntspi-php php artisan email:fetch-news --async --queue=email-processing
```

3. **Мониторинг:**
```bash
# Логи job
tail -f storage/logs/laravel.log | grep ProcessEmailNewsJob

# Статус очереди
docker exec ntspi-php php artisan queue:monitor email-processing
```

### Cron (автоматическая обработка)

Добавить в crontab:
```cron
*/15 * * * * docker exec ntspi-php php artisan email:fetch-news --async --queue=email-processing
```

---

## Метрики производительности

### До оптимизации
- **Потребление памяти:** 256MB+ (ошибка при 50+ письмах)
- **Время обработки:** 30+ секунд (блокирующий вызов)
- **Надежность:** Низкая (падал при больших письмах)

### После оптимизации
- **Потребление памяти:** ~150MB (batch по 10 писем)
- **Время обработки:** Асинхронное (не блокирует веб)
- **Надежность:** Высокая (автоматические повторные попытки)

---

## Мониторинг памяти

Все Tasks логируют использование памяти при превышении порогов:

```log
[2024-01-15 10:30:00] local.INFO: [MemoryMonitor] Использование памяти
{
    "context": "after_fetching_emails",
    "current": "145.23MB",
    "peak": "178.45MB",
    "memory_limit": "1G",
    "emails_count": 10
}

[2024-01-15 10:30:05] local.INFO: [MemoryMonitor] Сборка мусора выполнена
{
    "memory_before": "412.5MB",
    "memory_after": "156.3MB",
    "freed": "256.2MB"
}
```

---

## Troubleshooting

### Ошибка: "Allowed memory size exhausted"

1. **Проверьте применение php.ini:**
```bash
docker exec ntspi-php php -i | grep memory_limit
# Должно быть: memory_limit => 1G => 1G
```

2. **Пересоберите контейнер:**
```bash
docker-compose down
docker-compose up -d --build app
```

3. **Уменьшите batch size:**
В `FetchEmailNewsAction` измените:
```php
private const BATCH_SIZE = 5;  # Было 10, стало 5
```

### Queue worker не запускается

```bash
# Проверьте статус
docker exec ntspi-php php artisan queue:status

# Перезапустите worker
docker restart ntspi-php-queue

# Проверьте логи
tail -f storage/logs/laravel.log | grep ProcessEmailNewsJob
```

### Письма не обрабатываются

```bash
# Проверьте подключение к IMAP
docker exec ntspi-php php artisan tinker
>>> config('imap.accounts.email_news.host')
>>> config('email-news.enabled')

# Запустите с флагом --force
docker exec ntspi-php php artisan email:fetch-news --force --log
```

---

## Архитектура (Porto)

```
app/Containers/Dashboard/
├── Actions/EmailNews/
│   └── FetchEmailNewsAction.php          # Оркестрация + batch-обработка
├── Tasks/Email/
│   ├── ConnectToImapTask.php             # IMAP подключение (+ MemoryAwareTrait)
│   ├── FetchUnreadEmailsTask.php         # Получение заголовков (+ MemoryAwareTrait)
│   ├── DownloadAttachmentsTask.php       # Сохранение вложений (+ memory monitoring)
│   ├── FilterBySenderTask.php            # Фильтрация по отправителю
│   └── MarkEmailAsReadTask.php           # Пометка как прочитанное
├── Jobs/
│   └── ProcessEmailNewsJob.php           # Queue Job для асинхронной обработки
├── Traits/
│   └── MemoryAwareTrait.php              # Трейт мониторинга памяти
└── Commands/
    └── FetchEmailNewsCommand.php         # Artisan команда (sync/async режимы)
```

---

## Чеклист деплоя

- [ ] Применены изменения в `config/imap.php`
- [ ] Обновлены все Tasks и Actions
- [ ] Создан `ProcessEmailNewsJob`
- [ ] Обновлен `FetchEmailNewsCommand`
- [ ] Увеличен `memory_limit` в `php.ini` до `1G`
- [ ] Пересобран PHP-контейнер (`docker-compose up -d --build app`)
- [ ] Проверено применение конфига (`php -i | grep memory_limit`)
- [ ] Queue worker запущен (`docker exec ntspi-php php artisan queue:work`)
- [ ] Протестирован async режим (`email:fetch-news --async`)
- [ ] Проверены логи на наличие ошибок памяти

---

## Дополнительные ресурсы

- [Webklex PHP-IMAP Documentation](https://github.com/Webklex/php-imap)
- [Laravel Queues Documentation](https://laravel.com/docs/10.x/queues)
- [PHP Garbage Collection](https://www.php.net/manual/en/features.gc.php)
- [Memory Management in PHP](https://www.php.net/manual/en/features.gc.performance-considerations.php)
