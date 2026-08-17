@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h3 fw-bold mb-4">Login</h1>
                        <p class="text-muted">Sign in to your ISHEP account.</p>
                        <form method="POST" action="{{ route('login.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autocomplete="email" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" name="password" type="password" class="form-control" autocomplete="current-password" required>
                            </div>
                            <div class="form-check mb-3">
                                <input id="remember" name="remember" value="1" type="checkbox" class="form-check-input">
                                <label for="remember" class="form-check-label">Remember me</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Sign in</button>
                        </form>
                        <a class="d-inline-block mt-3" href="{{ route('password.request') }}">Forgot your password?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
