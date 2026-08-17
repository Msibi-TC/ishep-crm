<?php

namespace Database\Seeders;

use App\Enums\SystemRole;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            SystemRole::RegisteredUser->value => ['Registered User', 'Default public account role.'],
            SystemRole::Administrator->value => ['Administrator', 'User administration and content moderation.'],
            SystemRole::Finance->value => ['Finance', 'Payment, refund and finance reporting access.'],
            SystemRole::SuperUser->value => ['Super User / IT', 'Full system administration access.'],
        ];

        foreach ($roles as $code => [$name, $description]) {
            Role::updateOrCreate(['code' => $code], compact('name', 'description') + ['is_system' => true]);
        }

        $permissionCodes = [
            'profile.view_own', 'profile.update_own', 'membership.apply', 'membership.view_own',
            'documents.upload_own', 'payments.view_own', 'payments.manage', 'refunds.request',
            'refunds.process', 'certificates.view_own', 'users.view', 'users.create', 'users.review',
            'users.suspend', 'users.assign_roles', 'reports.view', 'notices.publish', 'jobs.post',
            'jobs.moderate', 'bursaries.post', 'bursaries.moderate', 'system.manage',
        ];

        foreach ($permissionCodes as $code) {
            Permission::updateOrCreate(
                ['code' => $code],
                ['name' => str($code)->replace(['.', '_'], ' ')->title()->toString()]
            );
        }

        $assignments = [
            SystemRole::RegisteredUser->value => [
                'profile.view_own', 'profile.update_own', 'membership.apply', 'membership.view_own',
                'documents.upload_own', 'payments.view_own', 'refunds.request', 'certificates.view_own',
            ],
            SystemRole::Administrator->value => [
                'users.view', 'users.create', 'users.review', 'users.suspend', 'users.assign_roles',
                'reports.view', 'notices.publish', 'jobs.post', 'jobs.moderate', 'bursaries.post',
                'bursaries.moderate',
            ],
            SystemRole::Finance->value => [
                'payments.view_own', 'payments.manage', 'refunds.request', 'refunds.process', 'reports.view',
            ],
            SystemRole::SuperUser->value => $permissionCodes,
        ];

        foreach ($assignments as $roleCode => $codes) {
            $permissionIds = Permission::whereIn('code', $codes)->pluck('id');
            Role::where('code', $roleCode)->firstOrFail()->permissions()->sync($permissionIds);
        }
    }
}
