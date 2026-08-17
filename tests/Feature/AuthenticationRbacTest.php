<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\SystemRole;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthenticationRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_registration_assigns_only_registered_user_and_normalises_email(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Public User', 'email' => ' USER@Example.COM ',
            'password' => 'Password1', 'password_confirmation' => 'Password1', 'terms' => '1',
            'role' => SystemRole::Administrator->value,
            'roles' => [SystemRole::SuperUser->value],
            'permissions' => ['system.manage'],
            'account_status' => AccountStatus::Suspended->value,
        ]);

        $response->assertRedirect(route('dashboard'));
        $user = User::where('email', 'user@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame(AccountStatus::Active, $user->account_status);
        $this->assertTrue($user->hasRole(SystemRole::RegisteredUser));
        $this->assertFalse($user->hasRole(SystemRole::Administrator));
    }

    public function test_registration_requires_terms_and_secure_password(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Public User', 'email' => 'user@example.com',
            'password' => 'weak', 'password_confirmation' => 'weak',
        ])->assertSessionHasErrors(['password', 'terms']);
    }

    public function test_login_succeeds_updates_last_login_and_logout_invalidates_authentication(): void
    {
        $user = User::factory()->create(['password' => 'Password1']);
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'Password1'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_invalid_credentials_are_rejected_with_generic_error(): void
    {
        User::factory()->create(['email' => 'user@example.com']);
        $this->post(route('login.store'), ['email' => 'user@example.com', 'password' => 'WrongPassword1'])
            ->assertSessionHasErrors(['email' => 'The provided credentials are invalid.']);
        $this->assertGuest();
    }

    public function test_login_is_throttled(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login.store'), ['email' => 'missing@example.com', 'password' => 'WrongPassword1']);
        }

        $response = $this->post(route('login.store'), ['email' => 'missing@example.com', 'password' => 'WrongPassword1']);
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Too many login attempts', session('errors')->first('email'));
    }

    public function test_suspended_and_deactivated_users_cannot_log_in(): void
    {
        foreach ([AccountStatus::Suspended, AccountStatus::Deactivated] as $status) {
            $user = User::factory()->create(['email' => $status->value.'@example.com', 'password' => 'Password1', 'account_status' => $status]);
            $this->post(route('login.store'), ['email' => $user->email, 'password' => 'Password1'])
                ->assertSessionHasErrors('email');
            $this->assertGuest();
        }
    }

    public function test_password_reset_link_and_password_reset_work(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $this->post(route('password.email'), ['email' => $user->email])->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);

        $token = Password::createToken($user);
        $this->post(route('password.update'), [
            'token' => $token, 'email' => $user->email,
            'password' => 'NewPassword1', 'password_confirmation' => 'NewPassword1',
        ])->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('NewPassword1', $user->fresh()->password));
    }

    public function test_dashboard_requires_authentication_and_allows_active_user(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get(route('dashboard'))->assertOk();
    }

    public function test_each_staff_dashboard_requires_its_matching_role(): void
    {
        $routes = [
            SystemRole::Administrator->value => 'dashboard.administrator',
            SystemRole::Finance->value => 'dashboard.finance',
            SystemRole::SuperUser->value => 'dashboard.super-user',
        ];

        foreach ($routes as $roleCode => $routeName) {
            $user = User::factory()->create();
            $this->assignRole($user, $roleCode);
            $this->actingAs($user)->get(route($routeName))->assertOk();

            $other = User::factory()->create();
            $this->actingAs($other)->get(route($routeName))->assertForbidden();
        }
    }

    public function test_role_and_permission_relationships_work(): void
    {
        $user = User::factory()->create();
        $this->assignRole($user, SystemRole::Finance->value);

        $this->assertTrue($user->hasRole(SystemRole::Finance));
        $this->assertTrue($user->hasPermission('payments.manage'));
        $this->assertTrue(Role::where('code', 'finance')->firstOrFail()->users->contains($user));
        $this->assertTrue(Permission::where('code', 'payments.manage')->firstOrFail()->roles()->where('code', 'finance')->exists());
    }

    public function test_seeders_are_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('roles', 4);
        $this->assertDatabaseCount('permissions', 22);
        $this->assertDatabaseCount('provinces', 9);
        $this->assertDatabaseCount('membership_types', 3);
        $this->assertDatabaseHas('membership_types', ['code' => 'student', 'fee' => 0]);
    }

    public function test_role_assignment_command_assigns_existing_role_without_duplicates(): void
    {
        $user = User::factory()->create(['email' => 'staff@example.com']);

        $this->artisan('users:assign-role', ['email' => $user->email, 'role' => 'administrator'])
            ->expectsOutput('Assigned administrator to staff@example.com.')->assertSuccessful();
        $this->artisan('users:assign-role', ['email' => $user->email, 'role' => 'administrator'])
            ->expectsOutput('staff@example.com already has the administrator role.')->assertSuccessful();

        $this->assertDatabaseCount('user_roles', 1);
        $this->assertDatabaseHas('user_roles', ['user_id' => $user->id, 'assigned_by' => null]);
    }

    public function test_role_assignment_command_rejects_missing_user_or_role(): void
    {
        $this->artisan('users:assign-role', ['email' => 'missing@example.com', 'role' => 'administrator'])->assertFailed();
        $user = User::factory()->create();
        $this->artisan('users:assign-role', ['email' => $user->email, 'role' => 'missing'])->assertFailed();
    }

    private function assignRole(User $user, string $roleCode): void
    {
        $role = Role::where('code', $roleCode)->firstOrFail();
        $user->roles()->attach($role->id, ['assigned_by' => null, 'assigned_at' => now()]);
    }
}
