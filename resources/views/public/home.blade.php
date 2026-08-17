@extends('layouts.app')

@section('content')
    <section class="hero-banner py-5">
        <div class="container py-5">
            <div class="row align-items-center gy-4">
                <div class="col-lg-7">
                    <p class="text-uppercase fw-semibold mb-3 text-warning">Member engagement platform</p>
                    <h1 class="display-5 fw-bold mb-3">A stronger professional community for ISHEP.</h1>
                    <p class="lead mb-4">Build a connected membership experience for professionals, employers, and learners through a secure CRM and public portal foundation.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('membership') }}" class="btn btn-warning btn-lg">Explore membership</a>
                        <a href="{{ route('verify.membership') }}" class="btn btn-outline-light btn-lg">Verify membership</a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 shadow-lg rounded-4">
                        <div class="card-body p-4">
                            <h2 class="h4 fw-bold text-primary mb-3">Portal snapshot</h2>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0">Membership management</li>
                                <li class="list-group-item px-0">Career opportunities</li>
                                <li class="list-group-item px-0">Bursary administration</li>
                                <li class="list-group-item px-0">Verified public records</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-4">
                <p class="text-uppercase fw-semibold text-primary mb-2">What the platform supports</p>
                <h2 class="fw-bold">A modular foundation for the future CRM</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card portal-card h-100">
                        <div class="card-body">
                            <span class="badge bg-primary rounded-pill w-auto align-self-start">Membership</span>
                            <h3 class="h5 fw-bold mb-0">Member records and onboarding</h3>
                            <p class="text-muted mb-0">A secure and scalable space for member administration and profile workflows.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card portal-card h-100">
                        <div class="card-body">
                            <span class="badge bg-success rounded-pill w-auto align-self-start">Careers</span>
                            <h3 class="h5 fw-bold mb-0">Opportunities and employer listings</h3>
                            <p class="text-muted mb-0">Coordinate stakeholder-facing career exposure while keeping the admin experience clean.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card portal-card h-100">
                        <div class="card-body">
                            <span class="badge bg-warning text-dark rounded-pill w-auto align-self-start">Bursaries</span>
                            <h3 class="h5 fw-bold mb-0">Funding and scholarship workflows</h3>
                            <p class="text-muted mb-0">Prepare a dedicated bursary path with clear statuses and transparent reference processes.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-muted py-5">
        <div class="container">
            <div class="row align-items-center gy-4">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-3">Built for a trusted, professional community</h2>
                    <p class="text-muted">This foundation is designed for durable growth, safe configuration, and clear separation between public-facing information and later administrative systems.</p>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-semibold">Foundation status</span>
                                <span class="badge bg-success">Ready</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-semibold">Database target</span>
                                <span>MySQL</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-semibold">Frontend</span>
                                <span>Bootstrap 5</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">Project scope</span>
                                <span>Foundation</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
