<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\AdditionalEducation\Models\AdditionalEducation;
use App\Containers\Dashboard\Actions\AdditionalEducations\CreateAdditionalEducationAction;
use App\Containers\Dashboard\Actions\AdditionalEducations\DeleteAdditionalEducationAction;
use App\Containers\Dashboard\Actions\AdditionalEducations\ListAdditionalEducationsAction;
use App\Containers\Dashboard\Actions\AdditionalEducations\UpdateAdditionalEducationAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreAdditionalEducationRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateAdditionalEducationRequest;
use App\Http\Controllers\Controller;
use App\Services\Filament\Domain\Seo\SeoGeneratorService;
use App\Ship\Contracts\SeoDescriptionInterface;
use App\Ship\Contracts\SeoTitleInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdditionalEducationController extends Controller
{
    public function __construct(
        private readonly ListAdditionalEducationsAction $listAdditionalEducationsAction,
        private readonly CreateAdditionalEducationAction $createAdditionalEducationAction,
        private readonly UpdateAdditionalEducationAction $updateAdditionalEducationAction,
        private readonly DeleteAdditionalEducationAction $deleteAdditionalEducationAction,
        private readonly SeoGeneratorService $seoGeneratorService,
    ) {}

    /**
     * Показывает список программ дополнительного образования
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'category_id', 'form_education', 'is_active']);

        $data = $this->listAdditionalEducationsAction->run($filters);

        return Inertia::render('Dashboard/AdditionalEducations/Index', $data);
    }

    /**
     * Показывает форму создания программы
     */
    public function create(): \Inertia\Response
    {
        $data = $this->listAdditionalEducationsAction->run([]);

        return Inertia::render('Dashboard/AdditionalEducations/Create', [
            'categories' => $data['categories'],
            'educationForms' => $data['educationForms'],
        ]);
    }

    /**
     * Создает новую программу дополнительного образования
     */
    public function store(StoreAdditionalEducationRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $education = $this->createAdditionalEducationAction->run($validated);

            $this->createSeo($education);

            return redirect()->route('dashboard.additional-educations.index')
                ->with('success', 'Программа дополнительного образования успешно создана!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании программы: ' . $e->getMessage());
        }
    }

    /**
     * Показывает форму редактирования программы
     */
    public function edit(AdditionalEducation $additionalEducation): \Inertia\Response
    {
        $additionalEducation->load(['category', 'seo']);

        $data = $this->listAdditionalEducationsAction->run([]);

        return Inertia::render('Dashboard/AdditionalEducations/Edit', [
            'education' => $additionalEducation,
            'categories' => $data['categories'],
            'educationForms' => $data['educationForms'],
        ]);
    }

    /**
     * Обновляет существующую программу дополнительного образования
     */
    public function update(UpdateAdditionalEducationRequest $request, AdditionalEducation $additionalEducation): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updateAdditionalEducationAction->run($additionalEducation, $validated);

            $this->updateSeo($additionalEducation);

            return redirect()->route('dashboard.additional-educations.index')
                ->with('success', 'Программа дополнительного образования успешно обновлена!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении программы: ' . $e->getMessage());
        }
    }

    /**
     * Удаляет программу дополнительного образования
     */
    public function destroy(AdditionalEducation $additionalEducation): RedirectResponse
    {
        try {
            $this->deleteAdditionalEducationAction->run($additionalEducation);

            return redirect()->route('dashboard.additional-educations.index')
                ->with('success', 'Программа дополнительного образования успешно удалена!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении программы: ' . $e->getMessage());
        }
    }

    /**
     * Генерирует и создает SEO данные
     */
    private function createSeo(AdditionalEducation $record): void
    {
        $seoData = $this->generateSeoData($record);
        $record->seo()->create($seoData);
    }

    /**
     * Обновляет SEO данные
     */
    private function updateSeo(AdditionalEducation $record): void
    {
        if ($record->seo()->exists()) {
            $record->seo()->update($this->generateSeoData($record));
        } else {
            $this->createSeo($record);
        }
    }

    /**
     * Генерирует SEO данные из записи
     */
    private function generateSeoData(AdditionalEducation $record): array
    {
        return $this->seoGeneratorService->generate([
            'title' => $record instanceof SeoTitleInterface
                ? $record->getSeoTitle()
                : $record->title,
            'content' => $record instanceof SeoDescriptionInterface
                ? $record->getSeoDescription()
                : ($record->content ?? []),
            'preview' => $record->preview ?? null,
        ]);
    }
}
