<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $response = Http::get('http://crawdad-fresh-bream.ngrok-free.app/api/import/users/')->object();
            foreach ($response as $user) {
                $userDetail = $user->user_detail;
                unset($user->user_detail);

                // Проверяем, существует ли пользователь с данным email
                $existingUser = User::where('email', $user->email)->first();

                if (!$existingUser) {
                    // Если пользователь не найден, создаём его
                    $newUser = User::create([
                        'name' => $user->name,
                        'slug' => $user->slug,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,
                        'password' => md5(Str::random(15)), // Убедитесь, что хэширование пароля соответствует вашим стандартам
                        'remember_token' => null,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ]);

                    // Создаем детали пользователя
                    $newUser->userDetail()->create([
                        'user_id' => $newUser->id,
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
                } else {
                    continue;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error fetching posts: ' . $e->getMessage());
        }
    }
}
