<?php

namespace App\Models;

use App\Enums\BillingPeriod;
use Illuminate\Database\Eloquent\Model;

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
}
