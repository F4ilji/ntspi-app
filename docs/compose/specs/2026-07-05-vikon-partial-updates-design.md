# VikonPartialUpdates — Частичные обновления модулей VIKON

## [S1] Проблема

Текущий модуль `VikonIntegration` поддерживает только полное обновление ядра модуля (`UpdateCoreAction`) и синхронизацию файлов через FM (`SyncFilesAction`). Частичные обновления (обновление отдельных частей модуля — common, struct, document...) не реализованы.

В `vikon_core` частичные обновления реализованы через 4-шаговый flow:
1. Запрос генерации части на сервере
2. Polling статуса генерации
3. Проверка готовности
4. Скачивание и применение

Нужно перенести эту логику в VikonIntegration с учётом архитектуры Porto.

---

## [S2] Обзор решения

**Backend-driven polling**: фронт отправляет один запрос `POST /update-part`, backend выполняет всю цепочку синхронно (request → polling → download → apply), возвращает результат. `HttpTask` уже устанавливает `set_time_limit(600)`, поэтому запрос может блокироваться до 10 минут — это достаточно для polling flow. Фронт показывает спиннер во время ожидания.

**Полная поддержка**: обычные части (atomic swap папки) + ABITUR (пофайловая синхронизация).

---

## [S3] Backend компоненты

### Новый Action: `UpdatePartAction`

**Расположение:** `app/Containers/VikonIntegration/Actions/UpdatePartAction.php`

**Зависимости:** `HttpTask`, `FilesystemTask`, `PollPartStatusTask`

**Интерфейс:**
```php
public function run(int $moduleId, string $part, string $accessToken): array
```

**Алгоритм:**
1. Валидация `moduleId` и `part` (whitelist из конфига)
2. Запрос генерации: `POST pull_updates/requestGeneratePartByNewCoreJson` с `part`
3. Получение `operation_identity` и `ttl`
4. Polling статуса через `PollPartStatusTask` (`GET pull_updates/getStatusPartGenerationByNewCoreJson`)
5. Проверка готовности: `POST pull_updates/checkPartGenerationByNewCoreResultJson`
6. Скачивание ZIP: `GET pull_updates/downloadPartByNewCoreResult?operation_identity=...&part=...`
7. Распаковка во временную директорию
8. Определение moduleId части (по `$allowedFoldersInCoreByModule`)
9. Применение:
   - **Обычная часть**: atomic swap всей папки
   - **ABITUR**: пофайловая синхронизация всех entry
10. `cleanUnitCore` с исключениями
11. Очистка temp файлов

**Возвращаемое значение:**
```php
['success' => true, 'message' => '...', 'synced_count' => int]
```

### Новый Task: `PollPartStatusTask`

**Расположение:** `app/Containers/VikonIntegration/Tasks/PollPartStatusTask.php`

**Интерфейс:**
```php
public function run(string $operationIdentity, string $accessToken, int $ttl = 120): array
```

**Алгоритм:**
1. Отправка GET `pull_updates/getStatusPartGenerationByNewCoreJson` с `operation_identity`
2. Если `status === "completed"` → возврат `['status' => 'completed']`
3. Если `status === "failed"` → возврат `['status' => 'failed', 'error' => ...]`
4. Если `status === "pending"` и время < `ttl` → sleep(3) и повтор
5. Если timeout → возврат `['status' => 'timeout']`

**Конфигурация:**
- Интервал polling: 3 секунды (configurable через конфиг)
- Max attempts: `ceil(ttl / interval)` + 2 (запас)

### Модификация `FilesystemTask`

Добавить метод `atomicSwap()`:
```php
public function atomicSwap(
    string $newEntryPath,
    string $currentEntryPath,
    string $baseDir,
    int $moduleId
): bool
```

**Алгоритм:**
1. Если `$currentEntryPath` существует:
   - Удалить `$currentEntryPath_new` если есть
   - Переместить `$newEntryPath` → `$currentEntryPath_new`
   - Удалить `$currentEntryPath_old` если есть
   - Переместить `$currentEntryPath` → `$currentEntryPath_old`
   - Переместить `$currentEntryPath_new` → `$currentEntryPath`
2. Если не существует:
   - Переместить `$newEntryPath` → `$currentEntryPath`

### Модификация `VikonController`

Добавить метод:
```php
public function updatePart(UpdatePartRequest $request): JsonResponse
```

**Маршрут:** `POST /dashboard/vikon-updates/update-part`

**Middleware:** `access-check`, `dashboard.auth`, `throttle:10,1`, `vikon.refresh`

---

## [S4] Frontend компоненты

### Модификация `Index.vue`

**Новые Props:**
- `available_parts`: объект с доступными частями по модулям

**Новые UI-элементы:**
- Выпадающий список частей для выбранного модуля
- Кнопка "Обновить часть" рядом с каждым модулем
- Прогресс-бар с логом во время обновления
- Индикатор загрузки (спиннер) во время polling

**Data flow:**
1. Пользователь выбирает модуль → показываются доступные части
2. Пользователь нажимает "Обновить" → отправляется `POST /update-part`
3. Фронт показывает спиннер/прогресс-бар пока backend выполняет цепочку
4. Получает ответ → показывает результат (успех/ошибка)

---

## [S5] API эндпоинты

### `POST /dashboard/vikon-updates/update-part`

**Request:**
```json
{
    "module_id": 1,
    "part": "common"
}
```

**Response (success):**
```json
{
    "success": true,
    "message": "Часть 'common' успешно обновлена.",
    "operation_identity": "...",
    "synced_count": 15
}
```

**Response (error):**
```json
{
    "success": false,
    "message": "Ошибка при генерации части: timeout"
}
```

---

## [S6] ABITUR special case

Для ABITUR (moduleId=2) в шаге 4 используется пофайловая синхронизация:

1. Сканирует содержимое распакованного ZIP
2. Для каждого entry (файл/папка) делает atomic swap через `FilesystemTask::atomicSwap()`
3. Исключает `files/` и `.htaccess` из `cleanUnitCore`
4. Не создаёт `sveden/update/index.php` redirect (это только для SVEDEN)

---

## [S7] Ошибки и откат

| Шаг | Ошибка | Действие |
|-----|--------|----------|
| Запрос генерации | HTTP ошибка | Возврат ошибки клиенту |
| Polling | Timeout (> ttl) | Возврат ошибки, очистка temp |
| Проверка готовности | Часть не готова | Возврат ошибки |
| Скачивание ZIP | HTTP ошибка | Возврат ошибки |
| Распаковка | Невалидный ZIP | Возврат ошибки |
| Atomic swap | Ошибка rename | `restoreUnitCoreAfterFail()` → откат через `_old` суффиксы |
| cleanUnitCore | Ошибка удаления | Логирование, продолжение |

---

## [S8] Конфигурация

Добавить в `config/vikon.php`:

```php
'parts' => [
    1 => ['common', 'struct', 'document', 'education', 'managers', 'employees', 'objects', 'paid_edu', 'budget', 'vacant', 'grants', 'inter', 'catering', 'eduStandarts', 'corruption', 'antiterrorism'],
    2 => ['abitur'],
    6 => ['general', 'structure', 'faq', 'procedures', 'results-and-reports', 'plans', 'survey'],
],
'poll_interval' => 3,
'poll_max_attempts' => 50,
```

---

## [S9] Зависимости

- Существующие: `HttpTask`, `FilesystemTask`, `ValidateTokenTask`
- Новые: `PollPartStatusTask`, `UpdatePartAction`
- Frontend: Vue 3 Composition API, Inertia.js

---

## [S10] Тестирование

1. **Unit-тест `PollPartStatusTask`**: mock HTTP, проверка polling loop
2. **Unit-тест `FilesystemTask::atomicSwap()`**: проверка 3-шагового flow
3. **Integration-тест `UpdatePartAction`**: mock API responses, проверка полного flow
4. **Manual test**: подключение к реальному API VIKON, обновление одной части
