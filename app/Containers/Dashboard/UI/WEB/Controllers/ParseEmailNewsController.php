<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\EmailNews\FetchEmailNewsAction;
use App\Containers\Dashboard\Exceptions\EmailFetchException;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ParseEmailNewsController extends Controller
{
    public function __construct(
        private readonly FetchEmailNewsAction $fetchEmailNewsAction,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        try {
            Log::info('[ParseEmailNewsController] Принудительный запуск парсинга email');

            $result = $this->fetchEmailNewsAction->run();

            if ($result['created_posts'] > 0) {
                return redirect()->back()->with('success', 
                    "Успешно обработано писем: {$result['processed_emails']}. Создано новостей: {$result['created_posts']}"
                );
            }

            if ($result['processed_emails'] === 0) {
                return redirect()->back()->with('info', 
                    'Нет непрочитанных писем для обработки'
                );
            }

            return redirect()->back()->with('warning', 
                "Обработано писем: {$result['processed_emails']}, но новостей не создано"
            );
        } catch (EmailFetchException $e) {
            Log::error('[ParseEmailNewsController] EmailFetchException', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('[ParseEmailNewsController] Критическая ошибка', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Ошибка при парсинге email: ' . $e->getMessage());
        }
    }
}
