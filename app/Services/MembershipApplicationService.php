<?php

namespace App\Services;

use App\Enums\ApplicationQueryStatus;
use App\Enums\MembershipApplicationStatus;
use App\Models\ApplicationQuery;
use App\Models\ApplicationStatusHistory;
use App\Models\AuditLog;
use App\Models\MembershipApplication;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MembershipApplicationService
{
    public function __construct(private StudentEligibilityService $students) {}

    public function createDraft(User $user, MembershipType $type): MembershipApplication
    {
        return DB::transaction(function () use ($user, $type) {
            $existing = $user->membershipApplications()->whereNotIn('status', [MembershipApplicationStatus::Approved->value, MembershipApplicationStatus::Rejected->value, MembershipApplicationStatus::Withdrawn->value])->lockForUpdate()->first();
            if ($existing) {
                return $existing;
            } $app = $user->membershipApplications()->create(['membership_type_id' => $type->id, 'reference_number' => 'APP-'.now()->format('Y').'-'.Str::upper(Str::random(10)), 'status' => MembershipApplicationStatus::Draft]);
            $this->history($app, null, MembershipApplicationStatus::Draft, $user);

            return $app;
        });
    }

    public function submit(MembershipApplication $app, User $user): MembershipApplication
    {
        return DB::transaction(function () use ($app, $user) {
            $app = MembershipApplication::lockForUpdate()->findOrFail($app->id);
            if (! in_array($app->status, [MembershipApplicationStatus::Draft, MembershipApplicationStatus::QuerySent], true)) {
                throw ValidationException::withMessages(['application' => 'This application cannot be submitted from its current status.']);
            }
            $app->load('membershipType', 'user.memberProfile', 'documents.documentType', 'studentEligibility');
            if (! $app->user->memberProfile) {
                throw ValidationException::withMessages(['profile' => 'Complete your member profile before submission.']);
            }
            $code = $app->membershipType->code;
            if ($code === 'company' && ! $app->organization_id) {
                throw ValidationException::withMessages(['organization' => 'Company details are required.']);
            }
            if ($code !== 'company' && $app->organization_id) {
                throw ValidationException::withMessages(['organization' => 'Only company applications may include an organization.']);
            }
            if (! $app->documents()->exists()) {
                throw ValidationException::withMessages(['documents' => 'At least one required document must be uploaded.']);
            }
            $documentCodes = $app->documents->pluck('documentType.code');
            if ($code === 'company' && ! $documentCodes->contains('company_registration')) {
                throw ValidationException::withMessages(['documents' => 'Company registration evidence is required.']);
            }
            if ($code === 'individual' && ! $documentCodes->contains('identity_document')) {
                throw ValidationException::withMessages(['documents' => 'Identity documentation is required.']);
            }
            if ($code === 'student') {
                $this->students->validateForSubmission($app);
            }
            $to = $app->status === MembershipApplicationStatus::QuerySent ? MembershipApplicationStatus::Resubmitted : MembershipApplicationStatus::Submitted;
            $from = $app->status;
            $app->forceFill(['status' => $to, 'submitted_at' => now(), 'declaration_accepted_at' => now()])->save();
            $this->history($app, $from, $to, $user);
            $this->audit($user, 'membership_application.submitted', $app);

            return $app;
        });
    }

    public function withdraw(MembershipApplication $app, User $user): void
    {
        $this->transition($app, MembershipApplicationStatus::Withdrawn, $user, 'Withdrawn by applicant', [MembershipApplicationStatus::Draft, MembershipApplicationStatus::Submitted, MembershipApplicationStatus::UnderReview, MembershipApplicationStatus::QuerySent, MembershipApplicationStatus::Resubmitted]);
    }

    public function respondToQuery(ApplicationQuery $query, User $user, string $response): void
    {
        DB::transaction(function () use ($query, $user, $response) {
            $query->forceFill(['response' => $response, 'responded_at' => now(), 'status' => ApplicationQueryStatus::Responded])->save();
            $this->audit($user, 'membership_query.responded', $query->application);
        });
    }

    public function transition(MembershipApplication $app, MembershipApplicationStatus $to, ?User $actor, ?string $reason, array $allowed): void
    {
        DB::transaction(function () use ($app, $to, $actor, $reason, $allowed) {
            $app = MembershipApplication::lockForUpdate()->findOrFail($app->id);
            if (! in_array($app->status, $allowed, true)) {
                throw ValidationException::withMessages(['status' => 'Invalid application status transition.']);
            } $from = $app->status;
            $app->forceFill(['status' => $to])->save();
            $this->history($app, $from, $to, $actor, $reason);
        });
    }

    private function history(MembershipApplication $app, ?MembershipApplicationStatus $from, MembershipApplicationStatus $to, ?User $actor, ?string $reason = null): void
    {
        ApplicationStatusHistory::create(['membership_application_id' => $app->id, 'from_status' => $from?->value, 'to_status' => $to->value, 'reason' => $reason, 'changed_by' => $actor?->id]);
    }

    public function audit(?User $actor, string $action, MembershipApplication $app, array $after = []): void
    {
        AuditLog::create(['actor_user_id' => $actor?->id, 'action' => $action, 'entity_type' => MembershipApplication::class, 'entity_id' => $app->id, 'after_values' => $after ?: ['status' => $app->status->value]]);
    }
}
