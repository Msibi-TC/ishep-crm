@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h3 fw-bold mb-4">Login</h1>
                        <p class="text-muted">This is a placeholder login screen for the future member and admin portal.</p>
                        <form>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input id="email" type="email" class="form-control" placeholder="name@example.com">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" type="password" class="form-control" placeholder="Password">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Sign in</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
