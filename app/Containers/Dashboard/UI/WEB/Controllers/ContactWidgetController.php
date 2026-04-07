<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\ContactWidgets\CreateContactWidgetAction;
use App\Containers\Dashboard\Actions\ContactWidgets\DeleteContactWidgetAction;
use App\Containers\Dashboard\Actions\ContactWidgets\ListContactWidgetsAction;
use App\Containers\Dashboard\Actions\ContactWidgets\UpdateContactWidgetAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreContactWidgetRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateContactWidgetRequest;
use App\Containers\Widget\Models\ContactWidget;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactWidgetController extends Controller
{
    public function __construct(
        private readonly ListContactWidgetsAction $listContactWidgetsAction,
        private readonly CreateContactWidgetAction $createContactWidgetAction,
        private readonly UpdateContactWidgetAction $updateContactWidgetAction,
        private readonly DeleteContactWidgetAction $deleteContactWidgetAction,
    ) {}

    /**
     * Показывает список контактных виджетов
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'is_active']);

        $data = $this->listContactWidgetsAction->run($filters);

        return Inertia::render('Dashboard/ContactWidgets/Index', $data);
    }

    /**
     * Показывает форму создания виджета
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Dashboard/ContactWidgets/Create');
    }

    /**
     * Создает новый контактный виджет
     */
    public function store(StoreContactWidgetRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->createContactWidgetAction->run($validated);

            return redirect()->route('dashboard.contact-widgets.index')
                ->with('success', 'Контактный виджет успешно создан!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании виджета: ' . $e->getMessage());
        }
    }

    /**
     * Показывает форму редактирования виджета
     */
    public function edit(ContactWidget $contactWidget): \Inertia\Response
    {
        return Inertia::render('Dashboard/ContactWidgets/Edit', [
            'widget' => $contactWidget,
        ]);
    }

    /**
     * Обновляет контактный виджет
     */
    public function update(UpdateContactWidgetRequest $request, ContactWidget $contactWidget): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updateContactWidgetAction->run($contactWidget, $validated);

            return redirect()->route('dashboard.contact-widgets.index')
                ->with('success', 'Контактный виджет успешно обновлён!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении виджета: ' . $e->getMessage());
        }
    }

    /**
     * Удаляет контактный виджет
     */
    public function destroy(ContactWidget $contactWidget): RedirectResponse
    {
        try {
            $this->deleteContactWidgetAction->run($contactWidget);

            return redirect()->route('dashboard.contact-widgets.index')
                ->with('success', 'Контактный виджет успешно удалён!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении виджета: ' . $e->getMessage());
        }
    }
}
