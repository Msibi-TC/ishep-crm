@extends('layouts.app')

@section('content')
    <div class="container py-5"><div class="row justify-content-center"><div class="col-lg-5">
        <div class="card shadow-sm border-0 rounded-4"><div class="card-body p-4 p-md-5">
            <h1 class="h3 fw-bold mb-3">Reset password</h1>
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="mb-3"><label for="email" class="form-label">Email address</label><input id="email" name="email" type="email" value="{{ old('email', $email) }}" class="form-control" autocomplete="email" required></div>
                <div class="mb-3"><label for="password" class="form-label">New password</label><input id="password" name="password" type="password" class="form-control" autocomplete="new-password" required></div>
                <div class="mb-3"><label for="password_confirmation" class="form-label">Confirm password</label><input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" required></div>
                <button class="btn btn-primary w-100" type="submit">Reset password</button>
            </form>
        </div></div>
    </div></div></div>
@endsection
