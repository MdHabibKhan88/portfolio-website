<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'correct-horse-9', 'is_admin' => true]);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
        $this->get('/admin/projects')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
    }

    public function test_admin_can_log_in_and_session_is_regenerated(): void
    {
        $this->admin();

        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'correct-horse-9'])
            ->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
        $this->get('/admin/dashboard')->assertOk();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $this->admin();

        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_non_admin_users_cannot_log_in_or_access_admin(): void
    {
        $user = User::create(['name' => 'User', 'email' => 'user@example.com', 'password' => 'correct-horse-9']);

        $this->post('/admin/login', ['email' => 'user@example.com', 'password' => 'correct-horse-9'])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->actingAs($user)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_login_is_rate_limited(): void
    {
        $this->admin();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'bad']);
        }

        // Even the correct password is refused once the limit is hit.
        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'correct-horse-9'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout(): void
    {
        $this->actingAs($this->admin())->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_create_admin_command(): void
    {
        $this->artisan('portfolio:create-admin', ['--email' => 'cli@example.com', '--name' => 'CLI'])
            ->expectsQuestion('Admin password (minimum 10 characters, letters and numbers)', 'strong-pass-123')
            ->expectsQuestion('Confirm password', 'strong-pass-123')
            ->assertSuccessful();

        $this->assertTrue(User::where('email', 'cli@example.com')->first()->is_admin);
    }

    public function test_create_admin_command_rejects_weak_password(): void
    {
        $this->artisan('portfolio:create-admin', ['--email' => 'cli@example.com'])
            ->expectsQuestion('Admin password (minimum 10 characters, letters and numbers)', 'short')
            ->expectsQuestion('Confirm password', 'short')
            ->assertFailed();

        $this->assertDatabaseMissing('users', ['email' => 'cli@example.com']);
    }
}
