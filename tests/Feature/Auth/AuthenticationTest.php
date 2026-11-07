<?php

namespace Tests\Feature\Auth;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_route_redirects_to_admin_login(): void
    {
        $response = $this->get('/login');

        $response->assertRedirect(route('filament.admin.auth.login'));
    }

    public function test_admin_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('filament.admin.auth.login'));

        $response->assertStatus(200);
    }

    public function test_admin_user_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => Role::SuperAdmin,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
