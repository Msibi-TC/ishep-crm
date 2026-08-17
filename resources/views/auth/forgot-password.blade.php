@extends('layouts.app')

@section('content')
    <div class="container py-5"><div class="row justify-content-center"><div class="col-lg-5">
        <div class="card shadow-sm border-0 rounded-4"><div class="card-body p-4 p-md-5">
            <h1 class="h3 fw-bold mb-3">Forgot password</h1>
            <p class="text-muted">Enter your email address to request a reset link.</p>
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <label for="email" class="form-label">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control mb-3" autocomplete="email" required autofocus>
                <button class="btn btn-primary w-100" type="submit">Email reset link</button>
            </form>
        </div></div>
    </div></div></div>
@endsection
