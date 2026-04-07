<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Education\Models\AdmissionCampaign;
use App\Containers\Dashboard\Actions\AdmissionCampaigns\CreateAdmissionCampaignAction;
use App\Containers\Dashboard\Actions\AdmissionCampaigns\DeleteAdmissionCampaignAction;
use App\Containers\Dashboard\Actions\AdmissionCampaigns\ListAdmissionCampaignsAction;
use App\Containers\Dashboard\Actions\AdmissionCampaigns\UpdateAdmissionCampaignAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreAdmissionCampaignRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateAdmissionCampaignRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdmissionCampaignController extends Controller
{
    public function __construct(
        private readonly ListAdmissionCampaignsAction $listAdmissionCampaignsAction,
        private readonly CreateAdmissionCampaignAction $createAdmissionCampaignAction,
        private readonly UpdateAdmissionCampaignAction $updateAdmissionCampaignAction,
        private readonly DeleteAdmissionCampaignAction $deleteAdmissionCampaignAction,
    ) {}

    /**
     * Показывает список приемных кампаний
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'status', 'academic_year']);
        $data = $this->listAdmissionCampaignsAction->run($filters);

        return Inertia::render('Dashboard/AdmissionCampaigns/Index', $data);
    }

    /**
     * Показывает форму создания кампании
     */
    public function create(): \Inertia\Response
    {
        $data = $this->listAdmissionCampaignsAction->run([]);

        return Inertia::render('Dashboard/AdmissionCampaigns/Create', [
            'statuses' => $data['statuses'],
            'academicYears' => $data['academicYears'],
        ]);
    }

    /**
     * Создает новую приемную кампанию
     */
    public function store(StoreAdmissionCampaignRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $this->createAdmissionCampaignAction->run($validated);

            return redirect()->route('dashboard.admission-campaigns.index')
                ->with('success', 'Приемная кампания успешно создана!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ошибка при создании кампании: ' . $e->getMessage());
        }
    }

    /**
     * Показывает форму редактирования кампании
     */
    public function edit(AdmissionCampaign $admissionCampaign): \Inertia\Response
    {
        $data = $this->listAdmissionCampaignsAction->run([]);

        return Inertia::render('Dashboard/AdmissionCampaigns/Edit', [
            'campaign' => $admissionCampaign,
            'statuses' => $data['statuses'],
            'academicYears' => $data['academicYears'],
        ]);
    }

    /**
     * Обновляет существующую приемную кампанию
     */
    public function update(UpdateAdmissionCampaignRequest $request, AdmissionCampaign $admissionCampaign): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $this->updateAdmissionCampaignAction->run($admissionCampaign, $validated);

            return redirect()->route('dashboard.admission-campaigns.index')
                ->with('success', 'Приемная кампания успешно обновлена!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ошибка при обновлении кампании: ' . $e->getMessage());
        }
    }

    /**
     * Удаляет приемную кампанию
     */
    public function destroy(AdmissionCampaign $admissionCampaign): RedirectResponse
    {
        try {
            $this->deleteAdmissionCampaignAction->run($admissionCampaign);

            return redirect()->route('dashboard.admission-campaigns.index')
                ->with('success', 'Приемная кампания успешно удалена!');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при удалении кампании: ' . $e->getMessage());
        }
    }
}
