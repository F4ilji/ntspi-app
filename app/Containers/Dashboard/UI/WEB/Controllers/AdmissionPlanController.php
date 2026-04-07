<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Education\Models\AdmissionPlan;
use App\Containers\Dashboard\Actions\AdmissionPlans\CreateAdmissionPlanAction;
use App\Containers\Dashboard\Actions\AdmissionPlans\DeleteAdmissionPlanAction;
use App\Containers\Dashboard\Actions\AdmissionPlans\ListAdmissionPlansAction;
use App\Containers\Dashboard\Actions\AdmissionPlans\UpdateAdmissionPlanAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreAdmissionPlanRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateAdmissionPlanRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class AdmissionPlanController extends Controller
{
    public function __construct(
        private readonly ListAdmissionPlansAction $listAdmissionPlansAction,
        private readonly CreateAdmissionPlanAction $createAdmissionPlanAction,
        private readonly UpdateAdmissionPlanAction $updateAdmissionPlanAction,
        private readonly DeleteAdmissionPlanAction $deleteAdmissionPlanAction,
    ) {}

    public function index(\Illuminate\Http\Request $request): \Inertia\Response
    {
        $filters = $request->only(['admission_campaigns_id', 'educational_programs_id']);
        $data = $this->listAdmissionPlansAction->run($filters);

        return Inertia::render('Dashboard/AdmissionPlans/Index', $data);
    }

    public function create(): \Inertia\Response
    {
        $data = $this->listAdmissionPlansAction->run([]);

        return Inertia::render('Dashboard/AdmissionPlans/Create', [
            'admissionCampaigns' => $data['admissionCampaigns'],
            'educationalPrograms' => $data['educationalPrograms'],
        ]);
    }

    public function store(StoreAdmissionPlanRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $this->createAdmissionPlanAction->run($validated);

            return redirect()->route('dashboard.admission-plans.index')
                ->with('success', 'План приема успешно создан!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ошибка при создании плана: ' . $e->getMessage());
        }
    }

    public function edit(AdmissionPlan $admissionPlan): \Inertia\Response
    {
        $admissionPlan->load(['educationalProgram', 'admissionCampaign']);
        $data = $this->listAdmissionPlansAction->run([]);

        return Inertia::render('Dashboard/AdmissionPlans/Edit', [
            'plan' => $admissionPlan,
            'admissionCampaigns' => $data['admissionCampaigns'],
            'educationalPrograms' => $data['educationalPrograms'],
        ]);
    }

    public function update(UpdateAdmissionPlanRequest $request, AdmissionPlan $admissionPlan): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $this->updateAdmissionPlanAction->run($admissionPlan, $validated);

            return redirect()->route('dashboard.admission-plans.index')
                ->with('success', 'План приема успешно обновлен!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ошибка при обновлении плана: ' . $e->getMessage());
        }
    }

    public function destroy(AdmissionPlan $admissionPlan): RedirectResponse
    {
        try {
            $this->deleteAdmissionPlanAction->run($admissionPlan);

            return redirect()->route('dashboard.admission-plans.index')
                ->with('success', 'План приема успешно удален!');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при удалении плана: ' . $e->getMessage());
        }
    }
}
