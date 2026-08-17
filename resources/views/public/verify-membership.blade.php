@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-5">
                        <p class="text-uppercase text-primary fw-semibold mb-3">Verification</p>
                        <h1 class="display-6 fw-bold mb-4">Membership verification</h1>
                        <p class="text-muted">This public verification page can later validate membership credentials and status without exposing private CRM data.</p>
                        <form class="row g-3">
                            <div class="col-md-8">
                                <label for="memberId" class="form-label">Membership reference</label>
                                <input id="memberId" type="text" class="form-control" placeholder="Enter member number">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Check</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
