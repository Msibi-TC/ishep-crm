<?php

namespace App\Enums;

enum StudentEligibilityStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Rejected = 'rejected';
}
