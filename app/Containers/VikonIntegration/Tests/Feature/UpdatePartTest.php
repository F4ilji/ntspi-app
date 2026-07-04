<?php

namespace App\Containers\VikonIntegration\Tests\Feature;

use App\Containers\User\Models\User;
use App\Ship\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdatePartTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::withoutEvents(fn () => User::create([
            'name' => 'Test User',
            'slug' => 'test-user',
            'email' => 'test-user-' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]));
    }

    public function test_update_part_requires_authentication(): void
    {
        $response = $this->postJson('/dashboard/vikon-updates/update-part', [
            'module_id' => 1,
            'part' => 'common',
        ]);

        $response->assertStatus(401);
    }

    public function test_update_part_validates_module_id(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->postJson('/dashboard/vikon-updates/update-part', [
            'module_id' => 999,
            'part' => 'common',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('module_id');
    }

    public function test_update_part_validates_part(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->postJson('/dashboard/vikon-updates/update-part', [
            'module_id' => 1,
            'part' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('part');
    }
}
