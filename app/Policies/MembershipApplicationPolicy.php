<?php

namespace App\Policies;

use App\Enums\MembershipApplicationStatus;
use App\Models\MembershipApplication;
use App\Models\User;

class MembershipApplicationPolicy
{
    public function view(User $u, MembershipApplication $a): bool
    {
        return $u->id === $a->user_id || $u->hasPermission('memberships.review');
    }

    public function update(User $u, MembershipApplication $a): bool
    {
        return $u->id === $a->user_id && in_array($a->status, [MembershipApplicationStatus::Draft, MembershipApplicationStatus::QuerySent], true);
    }
}
