<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_leads(): void
    {
        $response = $this->get('/leads');
        $response->assertRedirect('/login');
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertRedirect();
    }

    public function test_user_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
        $response->assertRedirect();

        $user = User::where('email', 'test@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('sales-rep'));
    }

    public function test_registration_provisions_missing_role(): void
    {
        Role::where('name', 'sales-rep')->delete();

        $response = $this->post('/register', [
            'name' => 'Role Provision User',
            'email' => 'role-provision@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', ['name' => 'sales-rep', 'guard_name' => 'web']);
        $this->assertTrue(User::where('email', 'role-provision@example.com')->firstOrFail()->hasRole('sales-rep'));
    }

    public function test_registration_rejects_privilege_escalation(): void
    {
        $response = $this->post('/register', [
            'name' => 'Escalation User',
            'email' => 'escalation@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',
            'is_admin' => '1',
        ]);

        $response->assertRedirect();
        $user = User::where('email', 'escalation@example.com')->firstOrFail();
        $this->assertFalse($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('super-admin'));
        $this->assertTrue($user->hasRole('sales-rep'));
    }

    public function test_role_seeding_is_idempotent(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->assertSame(1, Role::where('name', 'sales-rep')->where('guard_name', 'web')->count());
        $this->assertDatabaseHas('roles', ['name' => 'sales-rep', 'guard_name' => 'web']);
    }
}
