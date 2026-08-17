<?php

namespace App\Models;

use App\Enums\BillingPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipType extends Model
{
    protected $fillable = [
        'code', 'name', 'description', 'fee', 'billing_period', 'is_student', 'active',
    ];

    protected function casts(): array
    {
        return [
            'fee' => 'decimal:2',
            'billing_period' => BillingPeriod::class,
            'is_student' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(MembershipApplication::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }
}
