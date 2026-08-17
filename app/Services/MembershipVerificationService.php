<?php

namespace App\Services;

use App\Enums\MembershipStatus;
use App\Models\Membership;

class MembershipVerificationService
{
    public function verify(string $number): ?array
    {
        $m = Membership::with(['user.memberProfile', 'organization', 'membershipType'])->where('membership_number', strtoupper(trim($number)))->where('status', MembershipStatus::Active)->first();
        if (! $m) {
            return null;
        }

return ['display_name' => $m->organization?->name ?? trim(($m->user->memberProfile?->first_name ?? $m->user->name).' '.($m->user->memberProfile?->last_name ?? '')), 'membership_type' => $m->membershipType->name, 'status' => $m->status->value, 'renewal_date' => $m->renewal_date?->toDateString()];
    }
}
