<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Inertia\Inertia;
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