<?php

namespace App\Providers;

use App\Models\User;
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
    public function boot()
    {
        // Aquí defines el gate para verificar si el usuario tiene el rol de 'admin'
        Gate::define('access-Admin', function (User $user) {
            return $user->IdRol === 2;
        });
    }
}
