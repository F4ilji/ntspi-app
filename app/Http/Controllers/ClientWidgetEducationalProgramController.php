<?php

namespace App\Http\Controllers;

use App\Enums\EducationalProgramStatus;
use App\Http\Resources\EducationalProgramSearchResource;
use App\Models\EducationalProgram;
use Illuminate\Http\Request;

class ClientWidgetEducationalProgramController extends Controller
{
    public function index()
    {
        return EducationalProgramSearchResource::collection(
            EducationalProgram::query()
                ->where('status', EducationalProgramStatus::PUBLISHED)
                ->orderBy('name', 'desc')
                ->get());
    }
}
