<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\UserDetails\CreateUserDetailAction;
use App\Containers\Dashboard\Actions\UserDetails\DeleteUserDetailAction;
use App\Containers\Dashboard\Actions\UserDetails\UpdateUserDetailAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreUserDetailRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateUserDetailRequest;
use App\Containers\User\Models\User;
use App\Containers\User\Models\UserDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserDetailController extends Controller
{
    public function __construct(
        private readonly CreateUserDetailAction $createUserDetailAction,
        private readonly UpdateUserDetailAction $updateUserDetailAction,
        private readonly DeleteUserDetailAction $deleteUserDetailAction,
    ) {}

    /**
     * Show the form for creating user detail
     */
    public function create(User $user): Response
    {
        return Inertia::render('Dashboard/Users/UserDetail/Create', [
            'user' => $user->only(['id', 'name', 'email']),
        ]);
    }

    /**
     * Store a newly created user detail
     */
    public function store(StoreUserDetailRequest $request, User $user): RedirectResponse
    {
        try {
            $validated = $request->validated();

            // Обработка загруженного фото
            if ($request->hasFile('photo')) {
                $validated['photo'] = $this->handlePhotoUpload($request->file('photo'));
            }

            // Декодирование JSON полей
            $validated = $this->decodeJsonFields($validated);

            $this->createUserDetailAction->run($user->id, $validated);

            return redirect()->route('dashboard.users.edit', $user->id)
                ->with('success', 'Детальная информация успешно добавлена!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при добавлении информации: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing user detail
     */
    public function edit(User $user, UserDetail $userDetail): Response
    {
        // Проверяем принадлежность userDetail к user
        if ($userDetail->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        return Inertia::render('Dashboard/Users/UserDetail/Edit', [
            'user' => $user->only(['id', 'name', 'email']),
            'userDetail' => $userDetail,
        ]);
    }

    /**
     * Update the specified user detail
     */
    public function update(UpdateUserDetailRequest $request, User $user, UserDetail $userDetail): RedirectResponse
    {
        // Проверяем принадлежность userDetail к user
        if ($userDetail->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        try {
            $validated = $request->validated();

            // Обработка загруженного фото
            if ($request->hasFile('photo')) {
                $validated['photo'] = $this->handlePhotoUpload($request->file('photo'));
            }

            // Декодирование JSON полей
            $validated = $this->decodeJsonFields($validated);

            $this->updateUserDetailAction->run($userDetail, $validated);

            return redirect()->route('dashboard.users.edit', $user->id)
                ->with('success', 'Детальная информация успешно обновлена!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении информации: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user detail
     */
    public function destroy(User $user, UserDetail $userDetail): RedirectResponse
    {
        // Проверяем принадлежность userDetail к user
        if ($userDetail->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        try {
            $this->deleteUserDetailAction->run($userDetail);

            return redirect()->route('dashboard.users.edit', $user->id)
                ->with('success', 'Детальная информация успешно удалена!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении информации: ' . $e->getMessage());
        }
    }

    /**
     * Handle photo upload and return file path
     */
    private function handlePhotoUpload($file): string
    {
        $path = $file->store('images', 'public');
        return $path;
    }

    /**
     * Decode JSON fields from request data
     */
    private function decodeJsonFields(array $data): array
    {
        $jsonFields = [
            'workExperience',
            'education',
            'professionalRetraining',
            'professionalDevelopment',
            'awards',
            'professDisciplines',
            'attendedConferences',
            'publications',
            'other',
        ];

        foreach ($jsonFields as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = json_decode($data[$field], true);
            }
        }

        return $data;
    }
}
