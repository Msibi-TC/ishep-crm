<?php

namespace App\Models;

use App\Enums\MembershipApplicationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MembershipApplication extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['status' => MembershipApplicationStatus::class, 'submitted_at' => 'datetime', 'reviewed_at' => 'datetime', 'declaration_accepted_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membershipType(): BelongsTo
    {
        return $this->belongsTo(MembershipType::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function studentEligibility(): HasOne
    {
        return $this->hasOne(StudentEligibility::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ApplicationStatusHistory::class);
    }

    public function queries(): HasMany
    {
        return $this->hasMany(ApplicationQuery::class);
    }

    public function membership(): HasOne
    {
        return $this->hasOne(Membership::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
