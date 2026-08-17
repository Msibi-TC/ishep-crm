<?php

namespace Database\Seeders;

use App\Enums\BillingPeriod;
use App\Models\MembershipType;
use Illuminate\Database\Seeder;

class MembershipTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['company', 'Company', false],
            ['individual', 'Individual', false],
            ['student', 'Student', true],
        ] as [$code, $name, $isStudent]) {
            MembershipType::updateOrCreate(['code' => $code], [
                'name' => $name,
                'description' => 'Fee temporarily set to zero pending business confirmation.',
                'fee' => 0,
                'billing_period' => BillingPeriod::Annual,
                'is_student' => $isStudent,
                'active' => true,
            ]);
        }
    }
}
