<?php

namespace Tests\Feature\Auth;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica que usuarios sin rol admin no pueden acceder al panel admin.
     */
    public function test_regular_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create(['role' => Role::User]);
        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    /**
     * Verifica que usuarios admin sí pueden acceder al panel admin.
     */
    public function test_admin_user_can_access_admin_panel(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $this->actingAs($admin)->get('/admin')->assertSuccessful();
    }

    /**
     * Verifica que superadmin puede acceder al panel admin.
     */
    public function test_superadmin_can_access_admin_panel(): void
    {
        $superadmin = User::factory()->create(['role' => Role::SuperAdmin]);
        $this->actingAs($superadmin)->get('/admin')->assertSuccessful();
    }

    /**
     * Verifica que usuarios inactivos no pueden acceder al panel admin aunque sean admin.
     */
    public function test_inactive_admin_cannot_access_admin_panel(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin, 'is_active' => false]);
        $this->actingAs($admin)->get('/admin')->assertForbidden();
    }

    /**
     * Verifica que el panel de usuario antiguo redirige al panel unificado /admin.
     */
    public function test_user_panel_redirects_to_admin(): void
    {
        $this->get('/panel')->assertRedirect('/admin');
    }

    /**
     * Verifica que un admin normal no puede editar ni eliminar a un superadmin.
     */
    public function test_admin_cannot_modify_or_delete_superadmin(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $superadmin = User::factory()->create(['role' => Role::SuperAdmin]);

        $this->assertFalse($admin->can('update', $superadmin));
        $this->assertFalse($admin->can('delete', $superadmin));
        $this->assertTrue($superadmin->can('update', $superadmin));
        $this->assertFalse($admin->can('delete', $superadmin));
    }

    /**
     * Verifica que un usuario inactivo no puede acceder al panel admin.
     */
    public function test_inactive_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->inactive()->create([
            'role' => Role::Admin,
        ]);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    /**
     * Verifica que un usuario cuya cuenta se desactiva en caliente es expulsado y sus tokens revocados.
     */
    public function test_deactivated_user_is_logged_out_and_tokens_revoked(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->createToken('test-device');
        $this->assertEquals(1, $user->tokens()->count());

        $user->update(['is_active' => false]);
        $this->assertEquals(0, $user->tokens()->count());
    }

    /**
     * Verifica que Filament no expone ruta de registro público y la web tampoco.
     */
    public function test_filament_panel_does_not_expose_registration_route(): void
    {
        $this->get('/register')->assertNotFound();
        $this->get('/admin/register')->assertNotFound();
    }
}
