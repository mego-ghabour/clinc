<?php

namespace Tests\Feature;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_roles(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->get(route('roles.index'));

        $response->assertStatus(200);
        $response->assertSee('Super Admin');
    }

    public function test_can_create_new_role_with_permissions(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->post(route('roles.store'), [
            'name' => 'فني مختبر',
            'permissions' => ['view_patients'],
        ]);

        $response->assertRedirect();
        $role = Role::where('name', 'فني مختبر')->first();
        $this->assertNotNull($role);
        $this->assertTrue($role->hasPermissionTo('view_patients'));
    }

    public function test_can_update_role(): void
    {
        $this->authenticateSuperAdmin();
        $role = Role::create(['name' => 'دور تجريبي']);

        $response = $this->put(route('roles.update', $role->id), [
            'name' => 'دور معدل',
            'permissions' => ['view_patients', 'create_patients'],
        ]);

        $response->assertRedirect();
        $role->refresh();
        $this->assertEquals('دور معدل', $role->name);
        $this->assertTrue($role->hasPermissionTo('create_patients'));
    }

    public function test_cannot_update_super_admin_role(): void
    {
        $this->authenticateSuperAdmin();
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        $response = $this->put(route('roles.update', $superAdminRole->id), [
            'name' => 'Super Admin Modified',
        ]);

        $response->assertSessionHasErrors('error');
    }

    public function test_can_delete_role(): void
    {
        $this->authenticateSuperAdmin();
        $role = Role::create(['name' => 'دور للحذف']);

        $response = $this->delete(route('roles.destroy', $role->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_cannot_delete_super_admin_role(): void
    {
        $this->authenticateSuperAdmin();
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        $response = $this->delete(route('roles.destroy', $superAdminRole->id));

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('roles', ['name' => 'Super Admin']);
    }
}
