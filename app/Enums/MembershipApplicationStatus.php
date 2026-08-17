<?php

namespace App\Enums;

enum MembershipApplicationStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case QuerySent = 'query_sent';
    case Resubmitted = 'resubmitted';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Withdrawn = 'withdrawn';

    public function isTerminal(): bool
    {
        return in_array($this, [self::Approved, self::Rejected, self::Withdrawn], true);
    }
}
