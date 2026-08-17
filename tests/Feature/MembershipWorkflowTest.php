<?php

namespace Tests\Feature;

use App\Enums\DocumentReviewStatus;
use App\Enums\MembershipApplicationStatus;
use App\Enums\MembershipStatus;
use App\Enums\StudentCategory;
use App\Enums\StudentEligibilityStatus;
use App\Enums\SystemRole;
use App\Models\ApplicationQuery;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\MembershipApplication;
use App\Models\MembershipType;
use App\Models\Role;
use App\Models\User;
use App\Services\MembershipApplicationService;
use App\Services\MembershipApprovalService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MembershipWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_profile_can_be_created_and_updated(): void
    {
        $user = User::factory()->create();
        $data = ['first_name' => 'Ada', 'last_name' => 'Lovelace', 'phone' => '0123456789', 'date_of_birth' => '2000-01-01'];
        $this->actingAs($user)->put(route('member.profile.update'), $data)->assertRedirect();
        $this->assertDatabaseHas('member_profiles', ['user_id' => $user->id, 'first_name' => 'Ada', 'profile_status' => 'complete']);
        $this->actingAs($user)->put(route('member.profile.update'), $data + ['city' => 'Johannesburg'])->assertRedirect();
        $this->assertDatabaseCount('member_profiles', 1);
        $this->assertDatabaseHas('member_profiles', ['user_id' => $user->id, 'city' => 'Johannesburg']);
    }

    public function test_draft_creation_reuses_one_current_application(): void
    {
        $user = User::factory()->create();
        $type = MembershipType::where('code', 'individual')->firstOrFail();
        $this->actingAs($user)->post(route('member.applications.store'), ['membership_type_id' => $type->id])->assertRedirect();
        $this->actingAs($user)->post(route('member.applications.store'), ['membership_type_id' => $type->id])->assertRedirect();
        $this->assertDatabaseCount('membership_applications', 1);
        $this->assertDatabaseHas('application_status_history', ['to_status' => 'draft']);
    }

    public function test_company_requires_organization_and_individual_rejects_organization(): void
    {
        $company = $this->draft('company');
        $this->profile($company->user, 30);
        $this->document($company, 'company_registration');
        $this->expectValidation(fn () => app(MembershipApplicationService::class)->submit($company, $company->user), 'organization');

        $individual = $this->preparedApplication('individual');
        $organization = $individual->user->organizations()->create(['name' => 'Wrong Org', 'contact_email' => 'org@example.com', 'contact_phone' => '1']);
        $individual->update(['organization_id' => $organization->id]);
        $this->expectValidation(fn () => app(MembershipApplicationService::class)->submit($individual->fresh(), $individual->user), 'organization');
    }

    public function test_student_age_boundaries_are_inclusive_and_outside_ages_fail(): void
    {
        foreach ([18, 25] as $age) {
            $application = $this->preparedStudent($age, StudentCategory::Grade12, 'grade_12_proof');
            app(MembershipApplicationService::class)->submit($application, $application->user);
            $this->assertSame(MembershipApplicationStatus::Submitted, $application->fresh()->status);
        }
        foreach ([17, 26] as $age) {
            $application = $this->preparedStudent($age, StudentCategory::Grade12, 'grade_12_proof');
            $this->expectValidation(fn () => app(MembershipApplicationService::class)->submit($application, $application->user), 'date_of_birth');
        }
    }

    public function test_each_student_category_requires_matching_evidence(): void
    {
        foreach ([StudentCategory::Grade12, StudentCategory::ActiveTertiary, StudentCategory::ProspectiveTertiary] as $category) {
            $application = $this->preparedStudent(20, $category, null);
            $this->expectValidation(fn () => app(MembershipApplicationService::class)->submit($application, $application->user), 'documents');
        }
        foreach ([[StudentCategory::Grade12, 'grade_12_proof'], [StudentCategory::ActiveTertiary, 'tertiary_student_card'], [StudentCategory::ProspectiveTertiary, 'tertiary_application_proof']] as [$category, $proof]) {
            $application = $this->preparedStudent(20, $category, $proof);
            app(MembershipApplicationService::class)->submit($application, $application->user);
            $this->assertSame('submitted', $application->fresh()->status->value);
        }
    }

    public function test_secure_document_upload_validates_and_uses_random_private_path(): void
    {
        Storage::fake('local');
        $application = $this->draft('individual');
        $type = DocumentType::where('code', 'identity_document')->firstOrFail();
        $this->actingAs($application->user)->post(route('member.applications.documents.store', $application), [
            'document_type_id' => $type->id, 'document' => UploadedFile::fake()->create('identity.pdf', 100, 'application/pdf'),
        ])->assertRedirect();
        $document = Document::firstOrFail();
        $this->assertStringStartsWith('membership-documents/'.$application->user_id.'/', $document->getRawOriginal('storage_path'));
        $this->assertNotSame('identity.pdf', $document->stored_name);
        $this->assertSame(64, strlen($document->checksum));
        Storage::disk('local')->assertExists($document->getRawOriginal('storage_path'));

        $this->actingAs($application->user)->post(route('member.applications.documents.store', $application), ['document_type_id' => $type->id, 'document' => UploadedFile::fake()->create('bad.exe', 10, 'application/octet-stream')])->assertSessionHasErrors('document');
        $this->actingAs($application->user)->post(route('member.applications.documents.store', $application), ['document_type_id' => $type->id, 'document' => UploadedFile::fake()->create('large.pdf', 5121, 'application/pdf')])->assertSessionHasErrors('document');
    }

    public function test_document_download_is_owner_or_reviewer_only(): void
    {
        Storage::fake('local');
        $application = $this->preparedApplication('individual');
        $document = $application->documents()->firstOrFail();
        Storage::disk('local')->put($document->getRawOriginal('storage_path'), 'test');
        $this->actingAs($application->user)->get(route('documents.download', $document))->assertOk();
        $this->actingAs(User::factory()->create())->get(route('documents.download', $document))->assertForbidden();
        $reviewer = $this->reviewer();
        $this->actingAs($reviewer)->get(route('documents.download', $document))->assertOk();
    }

    public function test_submission_history_immutability_and_withdrawal(): void
    {
        $application = $this->preparedApplication('individual');
        $this->actingAs($application->user)->post(route('member.applications.submit', $application), ['declaration' => 1])->assertRedirect();
        $this->assertDatabaseHas('membership_applications', ['id' => $application->id, 'status' => 'submitted']);
        $this->assertDatabaseHas('application_status_history', ['membership_application_id' => $application->id, 'to_status' => 'submitted']);
        $this->actingAs($application->user)->put(route('member.applications.organization', $application), ['name' => 'No'])->assertForbidden();
        $this->actingAs($application->user)->post(route('member.applications.withdraw', $application))->assertRedirect();
        $this->assertSame('withdrawn', $application->fresh()->status->value);
    }

    public function test_missing_documents_prevent_submission(): void
    {
        $application = $this->draft('individual');
        $this->profile($application->user, 30);
        $this->actingAs($application->user)->post(route('member.applications.submit', $application), ['declaration' => 1])->assertSessionHasErrors('documents');
    }

    public function test_admin_queue_authorization_query_and_resubmission(): void
    {
        $application = $this->submitted('individual');
        $reviewer = $this->reviewer();
        $this->actingAs($reviewer)->get(route('admin.memberships.index'))->assertOk();
        $this->actingAs($reviewer)->get(route('admin.memberships.show', $application))->assertOk();
        $this->actingAs(User::factory()->create())->get(route('admin.memberships.index'))->assertForbidden();
        $this->actingAs($reviewer)->post(route('admin.memberships.query', $application), ['message' => 'Please clarify'])->assertRedirect();
        $query = ApplicationQuery::firstOrFail();
        $this->actingAs($application->user)->post(route('member.queries.respond', $query), ['response' => 'Clarified'])->assertRedirect();
        $this->actingAs($application->user)->post(route('member.applications.submit', $application), ['declaration' => 1])->assertRedirect();
        $this->assertSame('resubmitted', $application->fresh()->status->value);
    }

    public function test_invalid_transition_and_self_review_are_rejected(): void
    {
        $draft = $this->preparedApplication('individual');
        $reviewer = $this->reviewer();
        $this->actingAs($reviewer)->post(route('admin.memberships.approve', $draft))->assertSessionHasErrors('status');
        $this->giveReviewPermission($draft->user);
        $draft->update(['status' => MembershipApplicationStatus::Submitted]);
        $this->actingAs($draft->user)->get(route('admin.memberships.show', $draft))->assertForbidden();
    }

    public function test_rejection_requires_reason_and_records_history_and_audit(): void
    {
        $application = $this->submitted('individual');
        $reviewer = $this->reviewer();
        $this->actingAs($reviewer)->post(route('admin.memberships.reject', $application), [])->assertSessionHasErrors('reason');
        $this->actingAs($reviewer)->post(route('admin.memberships.reject', $application), ['reason' => 'Incomplete evidence'])->assertRedirect();
        $this->assertDatabaseHas('application_status_history', ['membership_application_id' => $application->id, 'to_status' => 'rejected']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'membership_application.rejected', 'entity_id' => $application->id]);
    }

    public function test_company_and_individual_approval_create_pending_payment_membership(): void
    {
        foreach (['company', 'individual'] as $code) {
            $application = $this->submitted($code);
            $membership = app(MembershipApprovalService::class)->approve($application, $this->reviewer(), 'Approved');
            $this->assertSame(MembershipStatus::PendingPayment, $membership->status);
            $this->assertNull($membership->membership_number);
        }
    }

    public function test_eligible_student_approval_activates_and_generates_number(): void
    {
        $application = $this->preparedStudent(20, StudentCategory::ActiveTertiary, 'tertiary_registration', true);
        app(MembershipApplicationService::class)->submit($application, $application->user);
        $membership = app(MembershipApprovalService::class)->approve($application->fresh(), $this->reviewer(), 'Eligible');
        $this->assertSame(MembershipStatus::Active, $membership->status);
        $this->assertMatchesRegularExpression('/^ISHEP-\d{4}-\d{6}$/', $membership->membership_number);
        $this->assertNotNull($membership->start_date);
        $this->assertNotNull($membership->renewal_date);
        $this->assertDatabaseHas('membership_status_history', ['membership_id' => $membership->id, 'to_status' => 'active']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'membership.approved', 'entity_id' => $membership->id]);
    }

    public function test_duplicate_current_membership_is_prevented(): void
    {
        $first = $this->submitted('individual');
        app(MembershipApprovalService::class)->approve($first, $this->reviewer());
        $second = $this->submitted('company', $first->user);
        $this->expectValidation(fn () => app(MembershipApprovalService::class)->approve($second, $this->reviewer()), 'membership');
    }

    public function test_public_verification_is_minimal_generic_and_throttled(): void
    {
        $application = $this->preparedStudent(20, StudentCategory::Grade12, 'grade_12_proof', true);
        app(MembershipApplicationService::class)->submit($application, $application->user);
        $membership = app(MembershipApprovalService::class)->approve($application->fresh(), $this->reviewer());
        $response = $this->post(route('verify.membership.submit'), ['membership_number' => $membership->membership_number]);
        $response->assertOk()->assertSee('Verified membership')->assertSee('Test Applicant')->assertDontSee($application->user->email)->assertDontSee($application->user->memberProfile->phone);
        $this->post(route('verify.membership.submit'), ['membership_number' => 'ISHEP-2026-999999'])->assertOk()->assertSee('could not be verified');
        for ($i = 0; $i < 9; $i++) {
            $this->post(route('verify.membership.submit'), ['membership_number' => 'ISHEP-2026-999998']);
        }
        $this->post(route('verify.membership.submit'), ['membership_number' => 'ISHEP-2026-999998'])->assertTooManyRequests();
    }

    public function test_idor_protection_for_applications_queries_and_documents(): void
    {
        $application = $this->preparedApplication('individual');
        $other = User::factory()->create();
        $this->actingAs($application->user)->get(route('member.applications.show', $application))->assertOk();
        $this->actingAs($other)->get(route('member.applications.show', $application))->assertForbidden();
        $query = ApplicationQuery::create(['membership_application_id' => $application->id, 'raised_by' => $this->reviewer()->id, 'message' => 'Question']);
        $this->actingAs($other)->post(route('member.queries.respond', $query), ['response' => 'Attack'])->assertForbidden();
    }

    private function draft(string $code, ?User $user = null): MembershipApplication
    {
        $user ??= User::factory()->create();

        return app(MembershipApplicationService::class)->createDraft($user, MembershipType::where('code', $code)->firstOrFail());
    }

    private function profile(User $user, int $age): void
    {
        $user->memberProfile()->updateOrCreate([], ['first_name' => 'Test', 'last_name' => 'Applicant', 'phone' => '0123456789', 'date_of_birth' => today()->subYears($age), 'profile_status' => 'complete']);
    }

    private function preparedApplication(string $code, ?User $user = null): MembershipApplication
    {
        $application = $this->draft($code, $user);
        $this->profile($application->user, 30);
        if ($code === 'company') {
            $organization = $application->user->organizations()->create(['name' => 'Example Company', 'contact_email' => 'company@example.com', 'contact_phone' => '1']);
            $application->update(['organization_id' => $organization->id]);
        }
        $this->document($application, $code === 'company' ? 'company_registration' : 'identity_document');

        return $application->fresh();
    }

    private function preparedStudent(int $age, StudentCategory $category, ?string $proof, bool $approved = false): MembershipApplication
    {
        $application = $this->draft('student');
        $this->profile($application->user, $age);
        $application->studentEligibility()->create(['category' => $category, 'eligibility_status' => $approved ? StudentEligibilityStatus::Verified : StudentEligibilityStatus::Pending]);
        if ($proof) {
            $this->document($application, $proof, $approved);
        }

        return $application->fresh();
    }

    private function document(MembershipApplication $application, string $code, bool $approved = false): Document
    {
        return Document::create(['owner_user_id' => $application->user_id, 'membership_application_id' => $application->id, 'document_type_id' => DocumentType::where('code', $code)->firstOrFail()->id, 'storage_disk' => 'local', 'storage_path' => 'membership-documents/'.$application->user_id.'/'.fake()->uuid().'.pdf', 'original_name' => 'proof.pdf', 'stored_name' => fake()->uuid().'.pdf', 'mime_type' => 'application/pdf', 'size_bytes' => 100, 'checksum' => hash('sha256', fake()->uuid()), 'review_status' => $approved ? DocumentReviewStatus::Approved : DocumentReviewStatus::Pending, 'uploaded_at' => now()]);
    }

    private function submitted(string $code, ?User $user = null): MembershipApplication
    {
        $application = $this->preparedApplication($code, $user);
        app(MembershipApplicationService::class)->submit($application, $application->user);

        return $application->fresh();
    }

    private function reviewer(): User
    {
        $user = User::factory()->create();
        $this->giveReviewPermission($user);

        return $user;
    }

    private function giveReviewPermission(User $user): void
    {
        $role = Role::where('code', SystemRole::Administrator->value)->firstOrFail();
        $user->roles()->syncWithoutDetaching([$role->id => ['assigned_at' => now()]]);
    }

    private function expectValidation(callable $callback, string $key): void
    {
        try {
            $callback();
            $this->fail('Expected validation exception.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey($key, $exception->errors());
        }
    }
}
