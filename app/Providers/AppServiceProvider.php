<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(fn ($user, string $ability) => $user->hasPermission($ability) ?: null);
        Blade::if('role', fn (string $role) => auth()->user()?->hasRole($role) ?? false);
        Blade::if('permission', fn (string $permission) => auth()->user()?->hasPermission($permission) ?? false);
    }
}
