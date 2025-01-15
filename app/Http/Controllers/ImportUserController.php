<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportUserController extends Controller
{
    public function index()
    {
        return User::query()->whereHas('userDetail')->with('userDetail')->get();
    }

    public function test()
    {
        $response = Http::get('http://crawdad-fresh-bream.ngrok-free.app/api/import/users/')->object();
        foreach ($response as $user) {
            $userDetail = $user->user_detail;
            unset($user->user_detail);
            $user = User::create([
                'name' => $user->name,
                'slug' => $user->slug,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'password' => Md5(Str::random(15)),
                'remember_token' => null,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);
            $user->userDetail()->create([
                'user_id' => $user->id,
                'is_only_worker' => $userDetail->is_only_worker,
                'photo' => $userDetail->photo,
                'academicTitle' => $userDetail->academicTitle,
                'AcademicDegree' => $userDetail->AcademicDegree,
                'education' => $userDetail->education,
                'awards' => $userDetail->awards,
                'professDisciplines' => $userDetail->professDisciplines,
                'professionalRetraining' => $userDetail->professionalRetraining,
                'professionalDevelopment' => $userDetail->professionalDevelopment,
                'workExperience' => $userDetail->workExperience,
                'attendedConferences' => $userDetail->attendedConferences,
                'participationScienceProjects' => $userDetail->participationScienceProjects,
                'publications' => $userDetail->publications,
                'contactEmail' => $userDetail->contactEmail,
                'contactPhone' => $userDetail->contactPhone,
                'search_data' => $userDetail->search_data,
                'other' => $userDetail->other,
                'created_at' => $userDetail->created_at,
                'updated_at' => $userDetail->updated_at,
            ]);
        }
    }
}
