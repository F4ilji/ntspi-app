<?php

namespace App\Containers\AdditionalEducation\Actions;

use App\Containers\AdditionalEducation\Data\Resources\AdditionalEducationResource;
use App\Containers\AdditionalEducation\Tasks\FindAdditionalEducationBySlugTask;
use App\Ship\Actions\Action;
use App\Ship\Contracts\SeoServiceInterface;
use Illuminate\Http\Request;

class GetAdditionalEducationBySlugAction
{
    public function __construct(
        readonly SeoServiceInterface $seoPageProvider,
        readonly FindAdditionalEducationBySlugTask $findAdditionalEducationBySlugTask,
    ) {}

    public function run(string $slug, Request $request): array
    {
        $additionalEducationModel = $this->findAdditionalEducationBySlugTask->run($slug);
        $seo = $this->seoPageProvider->getSeoForModel($additionalEducationModel);

        $settingsPage = $request->attributes->get('settings_page') ?? [];

        if (array_key_exists('custom_form', $settingsPage)) {
            $form = $settingsPage['custom_form'];
        } else {
            $form = null;
        }

        $additionalEducation = new AdditionalEducationResource($additionalEducationModel);

        return compact(
            'additionalEducation',
            'seo',
            'form',
        );
    }
}
