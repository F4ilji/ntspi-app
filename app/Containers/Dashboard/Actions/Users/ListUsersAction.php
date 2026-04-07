<?php

namespace App\Containers\Dashboard\Actions\Users;

use App\Containers\User\Models\User;
use Spatie\Permission\Models\Role;

class ListUsersAction
{
    public function run(array $filters = []): array
    {
        $query = User::with(['roles', 'userDetail']);

        // Поиск по имени или email
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Фильтр по роли
        if (!empty($filters['role_id'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('roles.id', $filters['role_id']);
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return [
            'users' => $users,
            'filters' => $filters,
            'roles' => Role::with('permissions')->orderBy('name')->get(['id', 'name']),
        ];
    }
}
