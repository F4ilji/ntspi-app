<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\IntegrationCredentials\ManageIntegrationCredentialsAction;
use App\Containers\Dashboard\Models\IntegrationCredential;
use App\Containers\Dashboard\UI\WEB\Requests\StoreIntegrationCredentialRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateIntegrationCredentialRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class IntegrationCredentialsController extends Controller
{
    public function __construct(
        private readonly ManageIntegrationCredentialsAction $action,
    ) {}

    public function index(): \Inertia\Response
    {
        return Inertia::render('Dashboard/IntegrationCredentials/Index', [
            'credentials' => $this->action->list(),
        ]);
    }

    public function create(): \Inertia\Response
    {
        return Inertia::render('Dashboard/IntegrationCredentials/Create');
    }

    public function store(StoreIntegrationCredentialRequest $request): RedirectResponse
    {
        try {
            $this->action->create($request->validated());

            return redirect()->route('dashboard.integration-credentials.index')
                ->with('success', 'Провайдер успешно добавлен!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при добавлении: ' . $e->getMessage());
        }
    }

    public function edit(IntegrationCredential $credential): \Inertia\Response
    {
        return Inertia::render('Dashboard/IntegrationCredentials/Edit', [
            'credential' => $credential,
        ]);
    }

    public function update(UpdateIntegrationCredentialRequest $request, IntegrationCredential $credential): RedirectResponse
    {
        try {
            $this->action->update($credential, $request->validated());

            return redirect()->route('dashboard.integration-credentials.index')
                ->with('success', 'Провайдер успешно обновлён!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении: ' . $e->getMessage());
        }
    }

    public function destroy(IntegrationCredential $credential): RedirectResponse
    {
        try {
            $this->action->delete($credential);

            return redirect()->route('dashboard.integration-credentials.index')
                ->with('success', 'Провайдер успешно удалён!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении: ' . $e->getMessage());
        }
    }
}
