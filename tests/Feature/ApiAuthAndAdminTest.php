<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiAuthAndAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_fetch_current_profile(): void
    {
        User::create([
            'first_name' => 'Ada',
            'last_name' => 'Client',
            'email' => 'ada@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'status' => 'Approved',
        ]);

        $login = $this->postJson('/api/auth/login', [
            'email' => 'ada@example.com',
            'password' => 'password',
        ])->assertOk()
            ->assertJsonPath('user.email', 'ada@example.com')
            ->assertJsonStructure(['token']);

        $this->withToken($login->json('token'))
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('user.role', 'client');
    }

    public function test_admin_can_manage_platform_services(): void
    {
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'Approved',
        ]);

        $token = $admin->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/admin/services', [
                'name' => 'NIN',
                'price' => 150,
                'description' => 'National Identification Number processing.',
                'status' => 'Active',
            ])
            ->assertCreated()
            ->assertJsonPath('service.name', 'NIN');

        $this->assertDatabaseHas(Service::class, [
            'name' => 'NIN',
            'status' => 'Active',
        ]);
    }
}
