@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h3 fw-bold mb-4">Create account</h1>
                        <p class="text-muted">Create a public account. Staff access is assigned only by authorised administrators.</p>
                        <form method="POST" action="{{ route('register.store') }}" class="row g-3">
                            @csrf
                            <div class="col-12">
                                <label for="name" class="form-label">Full name</label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" class="form-control" autocomplete="name" required autofocus>
                            </div>
                            <div class="col-12">
                                <label for="email" class="form-label">Email address</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control" autocomplete="email" required>
                            </div>
                            <div class="col-12">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" name="password" type="password" class="form-control" autocomplete="new-password" required>
                            </div>
                            <div class="col-12">
                                <label for="password_confirmation" class="form-label">Confirm password</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" required>
                            </div>
                            <div class="col-12 form-check ms-2">
                                <input id="terms" name="terms" value="1" type="checkbox" class="form-check-input" required>
                                <label for="terms" class="form-check-label">I accept the terms and conditions.</label>
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
