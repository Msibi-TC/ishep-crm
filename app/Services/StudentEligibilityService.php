<?php

namespace App\Services;

use App\Enums\StudentCategory;
use App\Models\MembershipApplication;
use Illuminate\Validation\ValidationException;

class StudentEligibilityService
{
    public function validateForSubmission(MembershipApplication $application): void
    {
        $profile = $application->user->memberProfile;
        $eligibility = $application->studentEligibility;
        if (! $profile?->date_of_birth || ! $eligibility) {
            throw ValidationException::withMessages(['student' => 'Student profile and eligibility details are required.']);
        }
        $age = $profile->date_of_birth->age;
        if ($age < 18 || $age > 25) {
            throw ValidationException::withMessages(['date_of_birth' => 'Student applicants must be between 18 and 25 inclusive at submission.']);
        }
        $required = match ($eligibility->category) {
            StudentCategory::Grade12 => ['grade_12_proof'],StudentCategory::ActiveTertiary => ['tertiary_student_card', 'tertiary_registration'],StudentCategory::ProspectiveTertiary => ['tertiary_application_proof']
        };
        $codes = $application->documents()->whereHas('documentType', fn ($q) => $q->whereIn('code', $required))->count();
        if ($codes < 1) {
            throw ValidationException::withMessages(['documents' => 'Required student evidence has not been uploaded.']);
        }
    }
}
