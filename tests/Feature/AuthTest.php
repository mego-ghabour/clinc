<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect(route('login'));
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('تسجيل الدخول');
    }

    public function test_user_can_login_using_email(): void
    {
        $user = User::factory()->create([
            'email' => 'doctor@example.com',
            'password' => Hash::make('secret123'),
            'status' => 'نشط',
        ]);

        $response = $this->post('/login', [
            'username' => 'doctor@example.com',
            'password' => 'secret123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_user_can_login_using_username(): void
    {
        $user = User::factory()->create([
            'username' => 'doc_ahmed',
            'password' => Hash::make('secret123'),
            'status' => 'نشط',
        ]);

        $response = $this->post('/login', [
            'username' => 'doc_ahmed',
            'password' => 'secret123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'username' => 'doc_ahmed',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post('/login', [
            'username' => 'doc_ahmed',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('username');
    }

    public function test_suspended_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'username' => 'suspended_doc',
            'password' => Hash::make('secret123'),
            'status' => 'موقوف',
        ]);

        $response = $this->post('/login', [
            'username' => 'suspended_doc',
            'password' => 'secret123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('username');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create(['status' => 'نشط']);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_visiting_login_redirects_to_dashboard(): void
    {
        $user = User::factory()->create(['status' => 'نشط']);

        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect(route('dashboard'));
    }
}
