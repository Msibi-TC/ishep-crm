<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationQueryStatus;
use App\Enums\DocumentReviewStatus;
use App\Enums\MembershipApplicationStatus;
use App\Enums\StudentEligibilityStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminQueryRequest;
use App\Http\Requests\ApproveApplicationRequest;
use App\Http\Requests\RejectApplicationRequest;
use App\Http\Requests\ReviewDocumentRequest;
use App\Models\ApplicationQuery;
use App\Models\Document;
use App\Models\MembershipApplication;
use App\Services\MembershipApplicationService;
use App\Services\MembershipApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembershipReviewController extends Controller
{
    public function index(Request $r): View
    {
        $q = MembershipApplication::with(['user', 'membershipType'])->latest();
        if ($r->filled('status')) {
            $q->where('status', $r->status);
        }if ($r->filled('membership_type_id')) {
            $q->where('membership_type_id', $r->membership_type_id);
        }

return view('admin.memberships.index', ['applications' => $q->paginate(25)]);
    }

    public function show(MembershipApplication $application): View
    {
        abort_if($application->user_id === auth()->id(), 403);

        return view('admin.memberships.show', ['application' => $application->load(['user.memberProfile', 'membershipType', 'organization', 'studentEligibility', 'documents.documentType', 'queries'])]);
    }

    public function query(AdminQueryRequest $r, MembershipApplication $application, MembershipApplicationService $s): RedirectResponse
    {
        abort_if($application->user_id === $r->user()->id, 403);
        ApplicationQuery::create(['membership_application_id' => $application->id, 'raised_by' => $r->user()->id, 'message' => $r->validated('message'), 'status' => ApplicationQueryStatus::Open]);
        $s->transition($application, MembershipApplicationStatus::QuerySent, $r->user(), 'Administrator query', [MembershipApplicationStatus::Submitted, MembershipApplicationStatus::Resubmitted, MembershipApplicationStatus::UnderReview]);

        return back()->with('status', 'Query sent.');
    }

    public function document(ReviewDocumentRequest $r, Document $document): RedirectResponse
    {
        $document->forceFill(['review_status' => DocumentReviewStatus::from($r->validated('review_status')), 'reviewed_by' => $r->user()->id, 'reviewed_at' => now(), 'review_notes' => $r->validated('review_notes')])->save();

        return back()->with('status', 'Document reviewed.');
    }

    public function eligibility(Request $r, MembershipApplication $application): RedirectResponse
    {
        $r->validate(['eligibility_status' => ['required', 'in:verified,rejected'], 'verification_notes' => ['nullable', 'string']]);
        abort_if($application->user_id === $r->user()->id, 403);
        $application->studentEligibility()->update(['eligibility_status' => StudentEligibilityStatus::from($r->eligibility_status), 'verified_by' => $r->user()->id, 'verified_at' => now(), 'verification_notes' => $r->verification_notes]);

        return back()->with('status', 'Eligibility reviewed.');
    }

    public function approve(ApproveApplicationRequest $r, MembershipApplication $application, MembershipApprovalService $s): RedirectResponse
    {
        $s->approve($application, $r->user(), $r->validated('reason') ?? 'Approved');

        return back()->with('status', 'Application approved.');
    }

    public function reject(RejectApplicationRequest $r, MembershipApplication $application, MembershipApprovalService $s): RedirectResponse
    {
        $s->reject($application, $r->user(), $r->validated('reason'));

        return back()->with('status','Application rejected.');
    }
}
