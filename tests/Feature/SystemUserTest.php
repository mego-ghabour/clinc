<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_system_users(): void
    {
        $this->authenticateSuperAdmin();
        $user = User::factory()->create(['name' => 'موظف تجريبي']);

        $response = $this->get(route('system-users.index'));

        $response->assertStatus(200);
        $response->assertSee('موظف تجريبي');
    }

    public function test_can_view_system_user_profile(): void
    {
        $this->authenticateSuperAdmin();
        $user = User::factory()->create(['name' => 'عبدالعزيز']);

        $response = $this->get(route('system-users.show', $user->id));

        $response->assertStatus(200);
        $response->assertSee('عبدالعزيز');
    }

    public function test_can_create_system_user(): void
    {
        $this->authenticateSuperAdmin();
        $role = Role::create(['name' => 'محاسب']);

        $response = $this->post(route('system-users.store'), [
            'name' => 'سالم الدوسري',
            'email' => 'salem@hospital.com',
            'username' => 'salem_d',
            'password' => 'Password123!',
            'roles' => ['محاسب'],
            'phone' => '0555123456',
        ]);

        $response->assertRedirect();
        $user = User::where('email', 'salem@hospital.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('محاسب'));
        $this->assertEquals('نشط', $user->status);
    }

    public function test_can_update_user_permissions(): void
    {
        $this->authenticateSuperAdmin();
        $user = User::factory()->create();
        $user->assignRole('ممرض');

        $response = $this->post(route('system_users.updatePermissions', $user->id), [
            'permissions' => ['view_patients', 'create_patients'],
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertTrue($user->hasDirectPermission('view_patients'));
        $this->assertTrue($user->hasDirectPermission('create_patients'));
    }

    public function test_can_toggle_user_status(): void
    {
        $this->authenticateSuperAdmin();
        $user = User::factory()->create(['status' => 'نشط']);

        $response = $this->patch(route('system_users.toggleStatus', $user->id));
        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals('موقوف', $user->status);

        $response = $this->patch(route('system_users.toggleStatus', $user->id));
        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals('نشط', $user->status);
    }

    public function test_cannot_suspend_super_admin(): void
    {
        $this->authenticateSuperAdmin();
        $superAdmin = User::factory()->create(['status' => 'نشط']);
        $superAdmin->assignRole('Super Admin');

        $response = $this->patch(route('system_users.toggleStatus', $superAdmin->id));

        $response->assertSessionHasErrors('error');
        $superAdmin->refresh();
        $this->assertEquals('نشط', $superAdmin->status);
    }

    public function test_user_cannot_suspend_themselves(): void
    {
        $currentUser = $this->authenticateSuperAdmin();

        $response = $this->patch(route('system_users.toggleStatus', $currentUser->id));

        $response->assertSessionHasErrors('error');
        $currentUser->refresh();
        $this->assertEquals('نشط', $currentUser->status);
    }
}
