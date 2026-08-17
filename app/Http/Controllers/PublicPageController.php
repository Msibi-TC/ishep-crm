<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function home(): View
    {
        return view('public.home');
    }

    public function membership(): View
    {
        return view('public.membership');
    }

    public function careers(): View
    {
        return view('public.careers');
    }

    public function bursaries(): View
    {
        return view('public.bursaries');
    }

    public function verifyMembership(): View
    {
        return view('public.verify-membership');
    }
}
