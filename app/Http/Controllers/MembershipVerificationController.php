<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicMembershipVerificationRequest;
use App\Services\MembershipVerificationService;
use Illuminate\View\View;

class MembershipVerificationController extends Controller
{
    public function show(): View
    {
        return view('public.verify-membership');
    }

    public function verify(PublicMembershipVerificationRequest $r, MembershipVerificationService $s): View
    {
        return view('public.verify-membership', ['verification' => $s->verify($r->validated('membership_number')), 'searched' => true]);
    }
}
