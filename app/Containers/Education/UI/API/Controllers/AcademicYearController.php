<?php

namespace App\Containers\Education\UI\API\Controllers;

use App\Containers\Education\Models\AdmissionCampaign;
use App\Ship\Controllers\Controller;

class AcademicYearController extends Controller
{
    public function __invoke()
    {
        $activeCampaign = AdmissionCampaign::query()->where('status', 1)->firstOrFail();
        return $activeCampaign->academic_year;
    }
}
