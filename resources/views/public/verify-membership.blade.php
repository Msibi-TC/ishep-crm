@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h1 class="h2">Membership Verification</h1>
                        <p class="text-muted">Enter an ISHEP membership number. Only minimal approved information is displayed.</p>
                        <form method="POST" action="{{ route('verify.membership.submit') }}" class="row g-3">
                            @csrf
                            <div class="col-md-8">
                                <label class="form-label" for="membership_number">Membership number</label>
                                <input class="form-control" id="membership_number" name="membership_number" value="{{ old('membership_number') }}" placeholder="ISHEP-2026-000001" required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end"><button class="btn btn-primary w-100">Verify</button></div>
                        </form>

                        @if (isset($searched))
                            <hr>
                            @if ($verification)
                                <div class="alert alert-success">
                                    <strong>Verified membership</strong>
                                    <dl class="row mb-0 mt-2">
                                        <dt class="col-sm-4">Display name</dt><dd class="col-sm-8">{{ $verification['display_name'] }}</dd>
                                        <dt class="col-sm-4">Type</dt><dd class="col-sm-8">{{ $verification['membership_type'] }}</dd>
                                        <dt class="col-sm-4">Status</dt><dd class="col-sm-8">{{ $verification['status'] }}</dd>
                                        <dt class="col-sm-4">Valid until</dt><dd class="col-sm-8">{{ $verification['renewal_date'] ?? 'Not available' }}</dd>
                                    </dl>
                                </div>
                            @else
                                <div class="alert alert-secondary">Membership could not be verified.</div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
