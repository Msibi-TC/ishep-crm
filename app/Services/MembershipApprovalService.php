<?php

namespace App\Services;

use App\Enums\DocumentReviewStatus;
use App\Enums\MembershipApplicationStatus;
use App\Enums\MembershipStatus;
use App\Enums\StudentEligibilityStatus;
use App\Models\AuditLog;
use App\Models\Membership;
use App\Models\MembershipApplication;
use App\Models\MembershipStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MembershipApprovalService
{
    public function __construct(private MembershipApplicationService $applications, private MembershipNumberService $numbers) {}

    public function approve(MembershipApplication $app, User $reviewer, string $reason = 'Approved'): Membership
    {
        return DB::transaction(function () use ($app, $reviewer, $reason) {
            $app = MembershipApplication::lockForUpdate()->with(['membershipType', 'studentEligibility', 'documents'])->findOrFail($app->id);
            if ($app->user_id === $reviewer->id) {
                abort(403);
            } if (! in_array($app->status, [MembershipApplicationStatus::Submitted, MembershipApplicationStatus::Resubmitted, MembershipApplicationStatus::UnderReview], true)) {
                throw ValidationException::withMessages(['status' => 'Application cannot be approved.']);
            } if (Membership::where('membership_application_id', $app->id)->exists() || Membership::where('user_id', $app->user_id)->whereIn('status', [MembershipStatus::PendingPayment->value, MembershipStatus::Active->value, MembershipStatus::RenewalDue->value, MembershipStatus::Suspended->value])->lockForUpdate()->exists()) {
                throw ValidationException::withMessages(['membership' => 'A current membership already exists.']);
            } $student = $app->membershipType->code === 'student';
            if ($student && ($app->studentEligibility?->eligibility_status !== StudentEligibilityStatus::Verified || $app->documents->isEmpty() || $app->documents->contains(fn ($d) => $d->review_status !== DocumentReviewStatus::Approved))) {
                throw ValidationException::withMessages(['student' => 'Student eligibility and required documents must be approved.']);
            } $status = $student ? MembershipStatus::Active : MembershipStatus::PendingPayment;
            $membership = Membership::create(['user_id' => $app->user_id, 'membership_application_id' => $app->id, 'membership_type_id' => $app->membership_type_id, 'organization_id' => $app->organization_id, 'status' => $status, 'start_date' => $student ? today() : null, 'renewal_date' => $student ? today()->addYear() : null, 'approved_by' => $reviewer->id, 'approved_at' => now()]);
            if ($student) {
                $membership->forceFill(['membership_number' => $this->numbers->generate($membership)])->save();
            } MembershipStatusHistory::create(['membership_id' => $membership->id, 'to_status' => $status->value, 'reason' => $reason, 'changed_by' => $reviewer->id]);
            $from = $app->status;
            $this->applications->transition($app, MembershipApplicationStatus::Approved, $reviewer, $reason, [$from]);
            $app->forceFill(['reviewed_by' => $reviewer->id, 'reviewed_at' => now(), 'decision_reason' => $reason])->save();
            AuditLog::create(['actor_user_id' => $reviewer->id, 'action' => 'membership.approved', 'entity_type' => Membership::class, 'entity_id' => $membership->id, 'after_values' => ['status' => $status->value]]);

            return $membership;
        });
    }

    public function reject(MembershipApplication $app, User $reviewer, string $reason): void
    {
        if (trim($reason) === '') {
            throw ValidationException::withMessages(['reason' => 'A rejection reason is required.']);
        }if ($app->user_id === $reviewer->id) {
            abort(403);
        }$app->forceFill(['reviewed_by' => $reviewer->id, 'reviewed_at' => now(), 'decision_reason' => $reason])->save();
        $this->applications->transition($app, MembershipApplicationStatus::Rejected, $reviewer, $reason, [MembershipApplicationStatus::Submitted, MembershipApplicationStatus::Resubmitted, MembershipApplicationStatus::UnderReview]);
        $this->applications->audit($reviewer, 'membership_application.rejected', $app);
    }
}
