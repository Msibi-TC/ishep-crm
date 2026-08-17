<?php

namespace App\Services;

use App\Models\Membership;

class MembershipNumberService
{
    public function generate(Membership $membership): string
    {
        return sprintf('ISHEP-%s-%06d', now()->format('Y'), $membership->id);
    }
}
