@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-5">
                        <p class="text-uppercase text-success fw-semibold mb-3">Careers</p>
                        <h1 class="display-6 fw-bold mb-4">Career opportunities and talent pathways</h1>
                        <p class="text-muted">This placeholder page supports the future careers portal and recruitment workflows.</p>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item px-0">Vacancy listings</li>
                            <li class="list-group-item px-0">Employer showcase</li>
                            <li class="list-group-item px-0">Application tracking</li>
                        </ul>
                        <a href="{{ route('home') }}" class="btn btn-success">Back to home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
