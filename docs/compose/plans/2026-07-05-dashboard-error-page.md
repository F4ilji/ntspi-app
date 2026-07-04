# Dashboard Error Page Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use compose:subagent (recommended) or compose:execute to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create a dedicated error page in the admin dashboard that shows full error context (status code, URL, message, stack trace, request params, user info, timestamp) with access restricted to super_admin users.

**Architecture:** Modify `Handler.php` to detect dashboard routes and render `Dashboard/DashboardError.vue` instead of `Error.vue`. The new component uses `DashboardLayout` and displays full error context as props from the backend.

**Tech Stack:** Laravel 10+, Vue 3 Composition API, Inertia.js, Tailwind CSS, Porto Architecture

## Global Constraints

- Code in English, comments in English
- Composition API (`<script setup>`) for all Vue components
- Use existing `DashboardLayout` component for page structure
- Use `route()` (Ziggy) for URL generation
- Follow Porto Architecture for backend changes
- Stack trace and request params only shown in non-production environments

---

## File Structure

| Action | File | Responsibility |
|--------|------|----------------|
| Create | `resources/js/Pages/Dashboard/DashboardError.vue` | Error page with DashboardLayout, full context display |
| Modify | `app/Exceptions/Handler.php` | Detect dashboard routes, render DashboardError.vue with context |
| Modify | `app/Ship/Exceptions/Handler.php` | Same changes as App Handler (duplicate) |

---

### Task 1: Create DashboardError.vue Component

**Covers:** S1 (Dashboard error page with full context)

**Files:**
- Create: `resources/js/Pages/Dashboard/DashboardError.vue`

**Interfaces:**
- Consumes: Props from Handler.php (status, message, url, method, stackTrace, requestParams, user, timestamp)
- Produces: Rendered error page in DashboardLayout

- [ ] **Step 1: Create the Vue component**

```vue
<template>
  <Head>
    <title>Ошибка {{ status }} — Панель управления</title>
  </Head>

  <DashboardLayout>
    <template #header-icon>
      <DashboardIcon name="exclamation-triangle" size="5" class="text-red-500" />
    </template>
    <template #header-title>Ошибка {{ status }}</template>
    <template #header-subtitle>{{ statusDescription }}</template>

    <div class="space-y-6">
      <!-- Error Code Card -->
      <div class="bg-layer rounded-xl border border-layer-line p-6">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 bg-red-50 rounded-xl flex items-center justify-center">
            <span class="text-3xl font-bold text-red-600">{{ status }}</span>
          </div>
          <div>
            <h2 class="text-lg font-semibold text-foreground">{{ statusDescription }}</h2>
            <p class="text-sm text-muted-foreground-1">{{ message }}</p>
          </div>
        </div>
      </div>

      <!-- Request Info Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- URL -->
        <div class="bg-layer rounded-xl border border-layer-line p-4">
          <h3 class="text-xs font-semibold text-muted-foreground-1 uppercase tracking-wide mb-2">
            URL запроса
          </h3>
          <p class="text-sm text-foreground font-mono break-all">{{ url }}</p>
        </div>

        <!-- Method -->
        <div class="bg-layer rounded-xl border border-layer-line p-4">
          <h3 class="text-xs font-semibold text-muted-foreground-1 uppercase tracking-wide mb-2">
            HTTP Метод
          </h3>
          <p class="text-sm text-foreground font-mono">{{ method }}</p>
        </div>

        <!-- User -->
        <div class="bg-layer rounded-xl border border-layer-line p-4">
          <h3 class="text-xs font-semibold text-muted-foreground-1 uppercase tracking-wide mb-2">
            Пользователь
          </h3>
          <p v-if="user" class="text-sm text-foreground">{{ user.name }} ({{ user.email }})</p>
          <p v-else class="text-sm text-muted-foreground-2">Не авторизован</p>
        </div>

        <!-- Timestamp -->
        <div class="bg-layer rounded-xl border border-layer-line p-4">
          <h3 class="text-xs font-semibold text-muted-foreground-1 uppercase tracking-wide mb-2">
            Время ошибки
          </h3>
          <p class="text-sm text-foreground">{{ formattedTimestamp }}</p>
        </div>
      </div>

      <!-- Stack Trace (non-production only) -->
      <div v-if="stackTrace" class="bg-layer rounded-xl border border-layer-line p-4">
        <button
          @click="showStack = !showStack"
          class="flex items-center gap-2 text-sm font-semibold text-foreground hover:text-primary transition-colors"
        >
          <DashboardIcon
            :name="showStack ? 'chevron-down' : 'chevron-right'"
            size="4"
          />
          Stack Trace
        </button>
        <div v-if="showStack" class="mt-4 overflow-x-auto">
          <pre class="text-xs text-muted-foreground-1 font-mono whitespace-pre-wrap bg-background-2 rounded-lg p-4">{{ stackTrace }}</pre>
        </div>
      </div>

      <!-- Request Params (non-production only) -->
      <div v-if="requestParams && Object.keys(requestParams).length" class="bg-layer rounded-xl border border-layer-line p-4">
        <button
          @click="showParams = !showParams"
          class="flex items-center gap-2 text-sm font-semibold text-foreground hover:text-primary transition-colors"
        >
          <DashboardIcon
            :name="showParams ? 'chevron-down' : 'chevron-right'"
            size="4"
          />
          Параметры запроса
        </button>
        <div v-if="showParams" class="mt-4 overflow-x-auto">
          <pre class="text-xs text-muted-foreground-1 font-mono whitespace-pre-wrap bg-background-2 rounded-lg p-4">{{ JSON.stringify(requestParams, null, 2) }}</pre>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-3">
        <button
          @click="goBack"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-layer border border-layer-line rounded-lg text-sm font-medium text-foreground hover:bg-primary-50 hover:text-primary transition-all"
        >
          <DashboardIcon name="arrow-left" size="4" />
          Назад
        </button>
        <a
          :href="route('dashboard.index')"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-light transition-all"
        >
          <DashboardIcon name="home" size="4" />
          На главную
        </a>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import DashboardLayout from './Components/DashboardLayout.vue';
import DashboardIcon from './Components/DashboardIcon.vue';

const props = defineProps({
  status: { type: Number, required: true },
  message: { type: String, default: '' },
  url: { type: String, default: '' },
  method: { type: String, default: 'GET' },
  stackTrace: { type: String, default: null },
  requestParams: { type: Object, default: () => ({}) },
  user: { type: Object, default: null },
  timestamp: { type: String, default: '' },
});

const showStack = ref(false);
const showParams = ref(false);

const statusDescription = computed(() => ({
  503: 'Сервис временно недоступен',
  500: 'Внутренняя ошибка сервера',
  404: 'Страница не найдена',
  403: 'Доступ запрещён',
})[props.status] || 'Неизвестная ошибка');

const formattedTimestamp = computed(() => {
  if (!props.timestamp) return '—';
  return new Date(props.timestamp).toLocaleString('ru-RU', {
    dateStyle: 'full',
    timeStyle: 'long',
  });
});

const goBack = () => {
  if (window.history.length > 1) {
    window.history.back();
  } else {
    window.location.href = route('dashboard.index');
  }
};
</script>
```

- [ ] **Step 2: Verify component structure**

Run: `grep -c "DashboardLayout" resources/js/Pages/Dashboard/DashboardError.vue`
Expected: `2` (import + template usage)

- [ ] **Step 3: Commit**

```bash
git add resources/js/Pages/Dashboard/DashboardError.vue
git commit -m "feat(dashboard): add DashboardError.vue with full error context display"
```

---

### Task 2: Modify Exception Handler for Dashboard Routes

**Covers:** S1, S2 (Route detection, context gathering)

**Files:**
- Modify: `app/Exceptions/Handler.php`
- Modify: `app/Ship/Exceptions/Handler.php`

**Interfaces:**
- Consumes: Request object, exception, user roles
- Produces: Inertia render with Dashboard/DashboardError.vue props

- [ ] **Step 1: Add helper method and modify render in App Handler**

Replace the `render` method in `app/Exceptions/Handler.php`:

```php
<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Prepare exception for rendering.
     *
     * @param  \Throwable  $e
     * @return \Throwable
     */
    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);
        $statusCode = $response->getStatusCode();

        if (in_array($statusCode, [500, 503, 404, 403])) {
            if ($this->isDashboardRequest($request)) {
                return $this->renderDashboardError($request, $e, $statusCode);
            }

            if (! app()->environment(['local', 'testing'])) {
                return Inertia::render('Error', ['status' => $statusCode])
                    ->toResponse($request)
                    ->setStatusCode($statusCode);
            }
        } elseif ($statusCode === 419) {
            return back()->with([
                'message' => 'The page expired, please try again.',
            ]);
        }

        return $response;
    }

    /**
     * Check if the request is for a dashboard route.
     */
    private function isDashboardRequest(Request $request): bool
    {
        return str_starts_with($request->path(), 'dashboard');
    }

    /**
     * Render error page with full context for dashboard users.
     */
    private function renderDashboardError(Request $request, Throwable $e, int $statusCode)
    {
        $user = $request->user();
        $isSuperAdmin = $user && $user->hasRole('super_admin');
        $isProduction = app()->environment('production');

        $props = [
            'status' => $statusCode,
            'message' => $this->getErrorMessage($e, $statusCode),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user' => $user ? $user->only('id', 'name', 'email') : null,
            'timestamp' => now()->toIso8601String(),
        ];

        // Only superadmins get stack trace and request params
        if ($isSuperAdmin && !$isProduction) {
            $props['stackTrace'] = $e->getTraceAsString();
            $props['requestParams'] = $request->except([
                'password',
                'password_confirmation',
                'current_password',
                '_token',
            ]);
        }

        return Inertia::render('Dashboard/DashboardError', $props)
            ->toResponse($request)
            ->setStatusCode($statusCode);
    }

    /**
     * Get a user-friendly error message based on status code.
     */
    private function getErrorMessage(Throwable $e, int $statusCode): string
    {
        $messages = [
            503 => 'Сервис временно недоступен. Попробуйте позже.',
            500 => 'Внутренняя ошибка сервера.',
            404 => 'Запрашиваемая страница не найдена.',
            403 => 'У вас нет доступа к этой странице.',
        ];

        return $messages[$statusCode] ?? 'Произошла неизвестная ошибка.';
    }
}
```

- [ ] **Step 2: Apply same changes to Ship Handler**

Replace the `render` method in `app/Ship/Exceptions/Handler.php`:

```php
<?php

namespace App\Ship\Exceptions;

use App\Ship\Abstracts\Exceptions\Handler as AbstractHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Throwable;

class Handler extends AbstractHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);
        $statusCode = $response->getStatusCode();

        if (in_array($statusCode, [500, 503, 404, 403])) {
            if ($this->isDashboardRequest($request)) {
                return $this->renderDashboardError($request, $e, $statusCode);
            }

            if (! app()->environment(['local', 'testing'])) {
                return Inertia::render('Error', ['status' => $statusCode])
                    ->toResponse($request)
                    ->setStatusCode($statusCode);
            }
        } elseif ($statusCode === 419) {
            return back()->with([
                'message' => 'The page expired, please try again.',
            ]);
        }

        return $response;
    }

    /**
     * Check if the request is for a dashboard route.
     */
    private function isDashboardRequest(Request $request): bool
    {
        return str_starts_with($request->path(), 'dashboard');
    }

    /**
     * Render error page with full context for dashboard users.
     */
    private function renderDashboardError(Request $request, Throwable $e, int $statusCode)
    {
        $user = $request->user();
        $isSuperAdmin = $user && $user->hasRole('super_admin');
        $isProduction = app()->environment('production');

        $props = [
            'status' => $statusCode,
            'message' => $this->getErrorMessage($e, $statusCode),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user' => $user ? $user->only('id', 'name', 'email') : null,
            'timestamp' => now()->toIso8601String(),
        ];

        // Only superadmins get stack trace and request params
        if ($isSuperAdmin && !$isProduction) {
            $props['stackTrace'] = $e->getTraceAsString();
            $props['requestParams'] = $request->except([
                'password',
                'password_confirmation',
                'current_password',
                '_token',
            ]);
        }

        return Inertia::render('Dashboard/DashboardError', $props)
            ->toResponse($request)
            ->setStatusCode($statusCode);
    }

    /**
     * Get a user-friendly error message based on status code.
     */
    private function getErrorMessage(Throwable $e, int $statusCode): string
    {
        $messages = [
            503 => 'Сервис временно недоступен. Попробуйте позже.',
            500 => 'Внутренняя ошибка сервера.',
            404 => 'Запрашиваемая страница не найдена.',
            403 => 'У вас нет доступа к этой странице.',
        ];

        return $messages[$statusCode] ?? 'Произошла неизвестная ошибка.';
    }
}
```

- [ ] **Step 3: Verify PHP syntax**

Run: `docker exec ntspi-php php -l app/Exceptions/Handler.php && docker exec ntspi-php php -l app/Ship/Exceptions/Handler.php`
Expected: No syntax errors

- [ ] **Step 4: Commit**

```bash
git add app/Exceptions/Handler.php app/Ship/Exceptions/Handler.php
git commit -m "feat(dashboard): modify exception handlers to render DashboardError for dashboard routes"
```

---

### Task 3: Verify End-to-End Flow

**Covers:** S1, S2, S3 (Full verification)

**Files:**
- Verify: `resources/js/Pages/Dashboard/DashboardError.vue`
- Verify: `app/Exceptions/Handler.php`
- Verify: `app/Ship/Exceptions/Handler.php`

- [ ] **Step 1: Test 404 on dashboard route**

Navigate to: `http://localhost/dashboard/nonexistent-page`
Expected: DashboardError page with status 404, dashboard layout visible

- [ ] **Step 2: Test 403 on dashboard route (non-superadmin)**

Login as non-superadmin user, navigate to restricted route
Expected: DashboardError page with status 403, no stack trace or params

- [ ] **Step 3: Test 403 on dashboard route (superadmin)**

Login as superadmin, trigger 403
Expected: DashboardError page with status 403, stack trace and params visible

- [ ] **Step 4: Test public site error (unchanged)**

Navigate to non-existent public page
Expected: Original Error.vue with public layout

- [ ] **Step 5: Final commit (if any fixes needed)**

```bash
git add -A
git commit -m "fix(dashboard): address review feedback on error page"
```
