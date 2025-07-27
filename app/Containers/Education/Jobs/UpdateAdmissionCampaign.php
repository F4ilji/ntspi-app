<?php

namespace App\Containers\Education\Jobs;

use App\Containers\Education\Models\AdmissionCampaign;
use App\Containers\Education\Models\AdmissionPlan;
use App\Containers\Education\Models\EducationalProgram;
use App\Services\Vicon\DirectionStudy\AdmissionPlanService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateAdmissionCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private string|int $id){}

    public function handle(): void
    {
//        $ac = AdmissionCampaign::query()->find($this->id);
//        $ap = AdmissionPlan::query()->all();
//        $uep = $ap->
    }
}