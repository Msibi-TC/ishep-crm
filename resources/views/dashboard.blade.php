@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h1 class="display-6 fw-bold">Dashboard</h1>
        <p class="lead">Welcome, {{ auth()->user()->name }}.</p>
        <p class="text-muted">Your account foundation is ready. Membership applications are not part of this release.</p>
    </div>
@endsection
