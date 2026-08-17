@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-5">
                        <p class="text-uppercase text-primary fw-semibold mb-3">Membership</p>
                        <h1 class="display-6 fw-bold mb-4">Professional membership portal</h1>
                        <p class="text-muted">This placeholder page is ready for future member onboarding, renewals, and account management screens.</p>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item px-0">Member directory</li>
                            <li class="list-group-item px-0">Renewal reminders</li>
                            <li class="list-group-item px-0">Subscriptions and categories</li>
                        </ul>
                        <a href="{{ route('home') }}" class="btn btn-primary">Back to home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
