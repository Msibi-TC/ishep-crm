<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AssignUserRole extends Command
{
    protected $signature = 'users:assign-role {email : Existing user email} {role : Existing role code}';

    protected $description = 'Assign an existing role to an existing user';

    public function handle(): int
    {
        $email = mb_strtolower(trim((string) $this->argument('email')));
        $roleCode = trim((string) $this->argument('role'));
        $user = User::where('email', $email)->first();
        $role = Role::where('code', $roleCode)->first();

        if (! $user) {
            $this->error("No user exists with email {$email}.");

            return self::FAILURE;
        }

        if (! $role) {
            $this->error("No role exists with code {$roleCode}.");

            return self::FAILURE;
        }

        if ($user->roles()->whereKey($role->id)->exists()) {
            $this->info("{$email} already has the {$roleCode} role.");

            return self::SUCCESS;
        }

        $user->roles()->attach($role->id, ['assigned_by' => null, 'assigned_at' => now()]);
        $this->info("Assigned {$roleCode} to {$email}.");

        return self::SUCCESS;
    }
}
