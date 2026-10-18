<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function authenticateSuperAdmin(): User
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create([
            'role' => 'Super Admin',
            'status' => 'نشط',
        ]);
        $user->assignRole('Super Admin');
        $this->actingAs($user);
        return $user;
    }

    protected function authenticateDoctor(): User
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create([
            'role' => 'طبيب',
            'status' => 'نشط',
        ]);
        $user->assignRole('طبيب');
        $this->actingAs($user);
        return $user;
    }
}
