<?php

use App\Http\Controllers\Admin\MembershipReviewController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\MemberProfileController;
use App\Http\Controllers\MembershipApplicationController;
use App\Http\Controllers\MembershipVerificationController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/membership', [PublicPageController::class, 'membership'])->name('membership');
Route::get('/careers', [PublicPageController::class, 'careers'])->name('careers');
Route::get('/bursaries', [PublicPageController::class, 'bursaries'])->name('bursaries');
Route::get('/verify-membership', [MembershipVerificationController::class, 'show'])->name('verify.membership');
Route::post('/verify-membership', [MembershipVerificationController::class, 'verify'])->middleware('throttle:10,1')->name('verify.membership.submit');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware(['auth', 'account.active'])->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/administrator', [DashboardController::class, 'administrator'])
        ->middleware('role:administrator')->name('dashboard.administrator');
    Route::get('/dashboard/finance', [DashboardController::class, 'finance'])
        ->middleware('role:finance')->name('dashboard.finance');
    Route::get('/dashboard/super-user', [DashboardController::class, 'superUser'])
        ->middleware('role:super_user')->name('dashboard.super-user');

    Route::get('/profile', [MemberProfileController::class, 'edit'])->name('member.profile.edit');
    Route::put('/profile', [MemberProfileController::class, 'update'])->name('member.profile.update');
    Route::get('/membership-applications', [MembershipApplicationController::class, 'index'])->name('member.applications.index');
    Route::get('/membership-applications/create', [MembershipApplicationController::class, 'create'])->name('member.applications.create');
    Route::post('/membership-applications', [MembershipApplicationController::class, 'store'])->name('member.applications.store');
    Route::get('/membership-applications/{application}', [MembershipApplicationController::class, 'show'])->name('member.applications.show');
    Route::put('/membership-applications/{application}/organization', [MembershipApplicationController::class, 'organization'])->name('member.applications.organization');
    Route::put('/membership-applications/{application}/student', [MembershipApplicationController::class, 'student'])->name('member.applications.student');
    Route::post('/membership-applications/{application}/documents', [DocumentController::class, 'store'])->name('member.applications.documents.store');
    Route::post('/membership-applications/{application}/submit', [MembershipApplicationController::class, 'submit'])->name('member.applications.submit');
    Route::post('/membership-applications/{application}/withdraw', [MembershipApplicationController::class, 'withdraw'])->name('member.applications.withdraw');
    Route::post('/application-queries/{query}/respond', [MembershipApplicationController::class, 'respond'])->name('member.queries.respond');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    Route::prefix('admin/membership-applications')->middleware('permission:memberships.review')->name('admin.memberships.')->group(function () {
        Route::get('/', [MembershipReviewController::class, 'index'])->name('index');
        Route::get('/{application}', [MembershipReviewController::class, 'show'])->name('show');
        Route::post('/{application}/query', [MembershipReviewController::class, 'query'])->name('query');
        Route::post('/{application}/eligibility', [MembershipReviewController::class, 'eligibility'])->name('eligibility');
        Route::post('/{application}/approve', [MembershipReviewController::class, 'approve'])->name('approve');
        Route::post('/{application}/reject', [MembershipReviewController::class, 'reject'])->name('reject');
        Route::post('/documents/{document}/review', [MembershipReviewController::class, 'document'])->name('documents.review');
    });
});
