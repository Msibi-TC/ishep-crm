<?php

namespace App\Http\Controllers;

use App\Enums\ProfileStatus;
use App\Http\Requests\UpdateMemberProfileRequest;
use App\Models\Profession;
use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MemberProfileController extends Controller
{
    public function edit(): View
    {
        return view('member.profile', ['profile' => auth()->user()->memberProfile, 'provinces' => Province::where('active', true)->get(), 'professions' => Profession::where('active', true)->get()]);
    }

    public function update(UpdateMemberProfileRequest $r): RedirectResponse
    {
        $data = $r->validated();
        $data['profile_status'] = ProfileStatus::Complete;
        auth()->user()->memberProfile()->updateOrCreate([], $data);

        return back()->with('status', 'Profile saved.');
    }
}
