<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Roles\CreateRoleAction;
use App\Containers\Dashboard\Actions\Roles\DeleteRoleAction;
use App\Containers\Dashboard\Actions\Roles\ListRolesAction;
use App\Containers\Dashboard\Actions\Roles\UpdateRoleAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreRoleRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateRoleRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function __construct(
        private readonly ListRolesAction $listRolesAction,
        private readonly CreateRoleAction $createRoleAction,
        private readonly UpdateRoleAction $updateRoleAction,
        private readonly DeleteRoleAction $deleteRoleAction,
    ) {}

    public function index(): Response
    {
        $data = $this->listRolesAction->run();

        return Inertia::render('Dashboard/Roles/Index', $data);
    }

    public function create(): Response
    {
        $permissions = Permission::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Dashboard/Roles/Create', [
            'permissions' => $permissions,
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        try {
            $this->createRoleAction->run($request->validated());

            return redirect()->route('dashboard.roles.index')
                ->with('success', 'Роль успешно создана!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании роли: ' . $e->getMessage());
        }
    }

    public function edit(Role $role): Response
    {
        $role->load('permissions');
        $permissions = Permission::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Dashboard/Roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->toArray(),
            ],
            'permissions' => $permissions,
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        try {
            $this->updateRoleAction->run($role, $request->validated());

            return redirect()->route('dashboard.roles.index')
                ->with('success', 'Роль успешно обновлена!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении роли: ' . $e->getMessage());
        }
    }

    public function destroy(Role $role): RedirectResponse
    {
        try {
            $this->deleteRoleAction->run($role);

            return redirect()->route('dashboard.roles.index')
                ->with('success', 'Роль успешно удалена!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении роли: ' . $e->getMessage());
        }
    }
}
