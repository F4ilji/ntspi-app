<?php

namespace App\Containers\Dashboard\Actions\Users;

use App\Containers\User\Models\User;
use Illuminate\Support\Str;

class UpdateUserAction
{
    public function run(User $user, array $data): User
    {
        // Генерация уникального slug при изменении имени
        if (isset($data['name']) && $data['name'] !== $user->name) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
        }

        // Если пароль пустой или null, удаляем из данных
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Извлекаем роли и пермисшены — они не в $fillable и не колонки таблицы
        $roles = $data['roles'] ?? null;
        $permissions = $data['permissions'] ?? null;
        unset($data['roles'], $data['permissions']);

        $user->update($data);

        // Синхронизация ролей
        if ($roles !== null && is_array($roles)) {
            $user->syncRoles($roles);
        }

        // Синхронизация разрешений
        if ($permissions !== null && is_array($permissions)) {
            $user->syncPermissions($permissions);
        }

        return $user->fresh(['roles', 'permissions']);
    }

    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $count = 1;
        $baseSlug = $slug;

        while (User::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
