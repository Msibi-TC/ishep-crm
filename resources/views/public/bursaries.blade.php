@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-5">
                        <p class="text-uppercase text-warning fw-semibold mb-3">Bursaries</p>
                        <h1 class="display-6 fw-bold mb-4">Grant and bursary administration</h1>
                        <p class="text-muted">This placeholder page is reserved for the eventual bursary application and review pipeline.</p>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item px-0">Funding categories</li>
                            <li class="list-group-item px-0">Application review</li>
                            <li class="list-group-item px-0">Award tracking</li>
                        </ul>
                        <a href="{{ route('home') }}" class="btn btn-warning text-dark">Back to home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
