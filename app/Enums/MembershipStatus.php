<?php

namespace App\Enums;

enum MembershipStatus: string
{
    case PendingPayment = 'pending_payment';
    case Active = 'active';
    case RenewalDue = 'renewal_due';
    case Suspended = 'suspended';
    case Expired = 'expired';
    case Deactivated = 'deactivated';

    public function isCurrent(): bool
    {
        return in_array($this, [self::PendingPayment, self::Active, self::RenewalDue, self::Suspended], true);
    }
}
