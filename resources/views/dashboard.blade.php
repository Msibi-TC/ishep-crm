@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h1 class="display-6 fw-bold">Dashboard</h1>
        <p class="lead">Welcome, {{ auth()->user()->name }}.</p>
        <p class="text-muted">Manage your profile, application, and membership status.</p>
        <div class="d-flex gap-2 flex-wrap"><a class="btn btn-primary" href="{{ route('member.profile.edit') }}">Complete profile</a><a class="btn btn-outline-primary" href="{{ route('member.applications.index') }}">Membership application</a></div>
    </div>
@endsection
