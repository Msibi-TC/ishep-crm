@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h3 fw-bold mb-4">Create account</h1>
                        <p class="text-muted">This placeholder registration page will later support member sign-up and onboarding flows.</p>
                        <form class="row g-3">
                            <div class="col-md-6">
                                <label for="firstName" class="form-label">First name</label>
                                <input id="firstName" type="text" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="lastName" class="form-label">Last name</label>
                                <input id="lastName" type="text" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="registerEmail" class="form-label">Email address</label>
                                <input id="registerEmail" type="email" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="registerPassword" class="form-label">Password</label>
                                <input id="registerPassword" type="password" class="form-control">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning w-100 text-dark">Register</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
