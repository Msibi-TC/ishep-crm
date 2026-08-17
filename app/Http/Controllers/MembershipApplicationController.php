<?php

namespace App\Http\Controllers;

use App\Enums\OrganizationStatus;
use App\Enums\StudentEligibilityStatus;
use App\Http\Requests\RespondToQueryRequest;
use App\Http\Requests\StoreDraftApplicationRequest;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\StoreStudentEligibilityRequest;
use App\Http\Requests\SubmitApplicationRequest;
use App\Models\ApplicationQuery;
use App\Models\DocumentType;
use App\Models\MembershipApplication;
use App\Models\MembershipType;
use App\Services\MembershipApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MembershipApplicationController extends Controller
{
    public function index(): View
    {
        return view('member.applications.index', ['applications' => auth()->user()->membershipApplications()->latest()->get(), 'membership' => auth()->user()->memberships()->latest()->first()]);
    }

    public function create(): View
    {
        return view('member.applications.create', ['types' => MembershipType::where('active', true)->get()]);
    }

    public function store(StoreDraftApplicationRequest $r, MembershipApplicationService $s): RedirectResponse
    {
        $a = $s->createDraft($r->user(), MembershipType::findOrFail($r->integer('membership_type_id')));

        return redirect()->route('member.applications.show', $a);
    }

    public function show(MembershipApplication $application): View
    {
        $this->authorize('view', $application);

        return view('member.applications.show', ['application' => $application->load(['membershipType', 'organization', 'studentEligibility', 'documents.documentType', 'queries', 'membership']), 'documentTypes' => DocumentType::where('active', true)->get()]);
    }

    public function organization(StoreOrganizationRequest $r, MembershipApplication $application): RedirectResponse
    {
        $this->authorize('update', $application);
        abort_unless($application->membershipType->code === 'company', 422);
        $org = $r->user()->organizations()->updateOrCreate(['id' => $application->organization_id], $r->validated() + ['status' => OrganizationStatus::Active]);
        $application->forceFill(['organization_id' => $org->id])->save();

        return back()->with('status', 'Organization details saved.');
    }

    public function student(StoreStudentEligibilityRequest $r, MembershipApplication $application): RedirectResponse
    {
        $this->authorize('update', $application);
        abort_unless($application->membershipType->code === 'student', 422);
        $application->studentEligibility()->updateOrCreate([], $r->validated() + ['eligibility_status' => StudentEligibilityStatus::Pending]);

        return back()->with('status', 'Student eligibility saved.');
    }

    public function submit(SubmitApplicationRequest $r, MembershipApplication $application, MembershipApplicationService $s): RedirectResponse
    {
        $this->authorize('update', $application);
        $s->submit($application, $r->user());

        return back()->with('status', 'Application submitted.');
    }

    public function withdraw(MembershipApplication $application, MembershipApplicationService $s): RedirectResponse
    {
        $this->authorize('view', $application);
        abort_unless(auth()->id() === $application->user_id, 403);
        $s->withdraw($application, auth()->user());

        return back()->with('status', 'Application withdrawn.');
    }

    public function respond(RespondToQueryRequest $r, ApplicationQuery $query, MembershipApplicationService $s): RedirectResponse
    {
        $this->authorize('update', $query->application);
        $s->respondToQuery($query, $r->user(), $r->validated('response'));

        return back()->with('status','Query response recorded.');
    }
}
