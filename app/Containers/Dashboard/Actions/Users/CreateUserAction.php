<?php

namespace App\Containers\Dashboard\Actions\Users;

use App\Containers\User\Models\User;
use Illuminate\Support\Str;

class CreateUserAction
{
    public function run(array $data): User
    {
        // Генерация уникального slug
        $data['slug'] = $this->generateUniqueSlug($data['name']);

        $user = User::create($data);

        // Назначение ролей если переданы
        if (isset($data['roles']) && is_array($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return $user->load(['roles', 'permissions']);
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
