<?php

namespace App\Models;

use App\Enums\ApplicationQueryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationQuery extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['status' => ApplicationQueryStatus::class, 'responded_at' => 'datetime', 'resolved_at' => 'datetime'];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(MembershipApplication::class, 'membership_application_id');
    }

    public function raisedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'raised_by');
    }
}
