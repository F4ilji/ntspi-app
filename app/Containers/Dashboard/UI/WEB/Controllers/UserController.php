<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Users\CreateUserAction;
use App\Containers\Dashboard\Actions\Users\DeleteUserAction;
use App\Containers\Dashboard\Actions\Users\InviteUserAction;
use App\Containers\Dashboard\Actions\Users\ListUsersAction;
use App\Containers\Dashboard\Actions\Users\UpdateUserAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreUserRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateUserRequest;
use App\Containers\User\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        private readonly ListUsersAction $listUsersAction,
        private readonly CreateUserAction $createUserAction,
        private readonly UpdateUserAction $updateUserAction,
        private readonly DeleteUserAction $deleteUserAction,
        private readonly InviteUserAction $inviteUserAction,
    ) {}

    /**
     * Display a listing of users
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'role_id']);

        $data = $this->listUsersAction->run($filters);

        return Inertia::render('Dashboard/Users/Index', $data);
    }

    /**
     * Show the form for creating a new user
     */
    public function create(): Response
    {
        $data = $this->listUsersAction->run([]);

        return Inertia::render('Dashboard/Users/Create', [
            'roles' => $data['roles'],
        ]);
    }

    /**
     * Store a newly created user
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->createUserAction->run($validated);

            return redirect()->route('dashboard.users.index')
                ->with('success', 'Пользователь успешно создан!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании пользователя: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user): Response
    {
        $user->load(['roles', 'permissions', 'userDetail']);

        $data = $this->listUsersAction->run([]);

        return Inertia::render('Dashboard/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'slug' => $user->slug,
                'roles' => $user->roles->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                ]),
                'permissions' => $user->permissions->map(fn($perm) => [
                    'name' => $perm->name,
                ]),
                'user_detail' => $user->userDetail,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'roles' => $data['roles']->map(fn($role) => [
                'id' => $role->id,
                'name' => $role->name,
            ]),
            'permissions' => $data['roles']->flatMap(function ($role) {
                return $role->permissions;
            })->unique('name')->values(),
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updateUserAction->run($user, $validated);

            return redirect()->route('dashboard.users.edit', $user->id)
                ->with('success', 'Пользователь успешно обновлен!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении пользователя: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            $this->deleteUserAction->run($user);

            return redirect()->route('dashboard.users.index')
                ->with('success', 'Пользователь успешно удален!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении пользователя: ' . $e->getMessage());
        }
    }

    /**
     * Invite a new user via email
     */
    public function invite(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $result = $this->inviteUserAction->run($request->email, auth()->id());

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
