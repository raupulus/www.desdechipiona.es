<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_route_redirects_to_admin_login(): void
    {
        $this->get('/login')->assertRedirect(route('filament.admin.auth.login'));
    }

    public function test_login_post_route_redirects_to_admin_login(): void
    {
        $this->post('/login')->assertRedirect(route('filament.admin.auth.login'));
    }

    public function test_panel_route_redirects_to_admin(): void
    {
        $this->get('/panel')->assertRedirect('/admin');
    }

    public function test_panel_login_route_redirects_to_admin(): void
    {
        $this->get('/panel/login')->assertRedirect('/admin');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/logout');
        $this->assertGuest();
    }
}
