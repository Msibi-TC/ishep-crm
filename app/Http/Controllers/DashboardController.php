<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard');
    }

    public function administrator(): View
    {
        return view('dashboards.administrator');
    }

    public function finance(): View
    {
        return view('dashboards.finance');
    }

    public function superUser(): View
    {
        return view('dashboards.super-user');
    }
}
