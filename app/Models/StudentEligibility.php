<?php

namespace App\Models;

use App\Enums\StudentCategory;
use App\Enums\StudentEligibilityStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentEligibility extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['category' => StudentCategory::class, 'eligibility_status' => StudentEligibilityStatus::class, 'verified_at' => 'datetime'];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(MembershipApplication::class, 'membership_application_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
