<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }}@isset($title) - {{ $title }}@endisset</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="page-shell">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">ISHEP</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-lg-2">
                        <li class="nav-item"><a class="nav-link" href="{{ route('membership') }}">Membership</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('careers') }}">Careers</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('bursaries') }}">Bursaries</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('verify.membership') }}">Verify membership</a></li>
                        @auth
                            <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                            @role('administrator')
                                <li class="nav-item"><a class="nav-link" href="{{ route('dashboard.administrator') }}">Administration</a></li>
                            @endrole
                            @role('finance')
                                <li class="nav-item"><a class="nav-link" href="{{ route('dashboard.finance') }}">Finance</a></li>
                            @endrole
                            @role('super_user')
                                <li class="nav-item"><a class="nav-link" href="{{ route('dashboard.super-user') }}">System</a></li>
                            @endrole
                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-light ms-lg-2">Logout</button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item"><a class="btn btn-outline-light ms-lg-2" href="{{ route('login') }}">Login</a></li>
                            <li class="nav-item"><a class="btn btn-warning ms-lg-2" href="{{ route('register') }}">Register</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            @if (session('status'))
                <div class="container mt-4 flash-message">
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="container mt-4 flash-message">
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="footer py-4 mt-5">
            <div class="container d-flex flex-column flex-md-row justify-content-between gap-2">
                <div>
                    <strong>ISHEP</strong> CRM & Portal Suite
                </div>
                <div>Membership • Careers • Bursaries</div>
            </div>
        </footer>
    </body>
</html>
