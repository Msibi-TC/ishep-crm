<?php

use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/membership', [PublicPageController::class, 'membership'])->name('membership');
Route::get('/careers', [PublicPageController::class, 'careers'])->name('careers');
Route::get('/bursaries', [PublicPageController::class, 'bursaries'])->name('bursaries');
Route::get('/verify-membership', [PublicPageController::class, 'verifyMembership'])->name('verify.membership');
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
