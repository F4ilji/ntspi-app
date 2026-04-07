<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\CustomForms\CreateCustomFormAction;
use App\Containers\Dashboard\Actions\CustomForms\DeleteCustomFormAction;
use App\Containers\Dashboard\Actions\CustomForms\DeleteFormResponseAction;
use App\Containers\Dashboard\Actions\CustomForms\ListCustomFormsAction;
use App\Containers\Dashboard\Actions\CustomForms\ListFormResponsesAction;
use App\Containers\Dashboard\Actions\CustomForms\UpdateCustomFormAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreCustomFormRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateCustomFormRequest;
use App\Containers\Widget\Models\CustomForm;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomFormController extends Controller
{
    public function __construct(
        private readonly ListCustomFormsAction $listCustomFormsAction,
        private readonly CreateCustomFormAction $createCustomFormAction,
        private readonly UpdateCustomFormAction $updateCustomFormAction,
        private readonly DeleteCustomFormAction $deleteCustomFormAction,
        private readonly ListFormResponsesAction $listFormResponsesAction,
        private readonly DeleteFormResponseAction $deleteFormResponseAction,
    ) {}

    /**
     * Показывает список пользовательских форм
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'status']);

        $data = $this->listCustomFormsAction->run($filters);

        return Inertia::render('Dashboard/CustomForms/Index', $data);
    }

    /**
     * Показывает форму создания формы
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Dashboard/CustomForms/Create');
    }

    /**
     * Создает новую пользовательскую форму
     */
    public function store(StoreCustomFormRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->createCustomFormAction->run($validated);

            return redirect()->route('dashboard.custom-forms.index')
                ->with('success', 'Пользовательская форма успешно создана!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании формы: ' . $e->getMessage());
        }
    }

    /**
     * Показывает форму редактирования формы
     */
    public function edit(CustomForm $customForm): \Inertia\Response
    {
        return Inertia::render('Dashboard/CustomForms/Edit', [
            'form' => $customForm,
        ]);
    }

    /**
     * Обновляет пользовательскую форму
     */
    public function update(UpdateCustomFormRequest $request, CustomForm $customForm): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updateCustomFormAction->run($customForm, $validated);

            return redirect()->route('dashboard.custom-forms.index')
                ->with('success', 'Пользовательская форма успешно обновлена!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении формы: ' . $e->getMessage());
        }
    }

    /**
     * Удаляет пользовательскую форму
     */
    public function destroy(CustomForm $customForm): RedirectResponse
    {
        try {
            $this->deleteCustomFormAction->run($customForm);

            return redirect()->route('dashboard.custom-forms.index')
                ->with('success', 'Пользовательская форма успешно удалена!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении формы: ' . $e->getMessage());
        }
    }

    /**
     * Показывает ответы на форму
     */
    public function responses(CustomForm $customForm, \Illuminate\Http\Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'checked']);

        $data = $this->listFormResponsesAction->run($customForm, $filters);

        return Inertia::render('Dashboard/CustomForms/Responses', $data);
    }

    /**
     * Переключает статус просмотра ответа
     */
    public function toggleResponseChecked(CustomForm $customForm, \App\Containers\Widget\Models\CustomFormResponse $response): RedirectResponse
    {
        $response->update(['checked' => !$response->checked]);

        return back();
    }

    /**
     * Удаляет ответ на форму
     */
    public function destroyResponse(CustomForm $customForm, \App\Containers\Widget\Models\CustomFormResponse $response): RedirectResponse
    {
        try {
            $this->deleteFormResponseAction->run($response);

            return back()->with('success', 'Ответ успешно удал!');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при удалении ответа: ' . $e->getMessage());
        }
    }
}
