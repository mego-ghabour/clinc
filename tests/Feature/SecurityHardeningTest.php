<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_user_cannot_access_settings(): void
    {
        $doctor = $this->authenticateDoctor();

        $response = $this->get(route('settings.index'));
        $response->assertStatus(403);
    }

    public function test_non_admin_user_cannot_access_roles(): void
    {
        $doctor = $this->authenticateDoctor();

        $response = $this->get(route('roles.index'));
        $response->assertStatus(403);
    }

    public function test_non_admin_user_cannot_access_system_users(): void
    {
        $doctor = $this->authenticateDoctor();

        $response = $this->get(route('system-users.index'));
        $response->assertStatus(403);
    }

    public function test_non_admin_user_cannot_access_audit_logs(): void
    {
        $doctor = $this->authenticateDoctor();

        $response = $this->get(route('audit_logs.index'));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_all_admin_routes(): void
    {
        $this->authenticateSuperAdmin();

        $this->get(route('settings.index'))->assertStatus(200);
        $this->get(route('roles.index'))->assertStatus(200);
        $this->get(route('system-users.index'))->assertStatus(200);
        $this->get(route('audit_logs.index'))->assertStatus(200);
    }
}
