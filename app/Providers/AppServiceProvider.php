<?php

namespace App\Providers;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // The production baseline dump already contains the `personal_access_tokens`
        // table, so Sanctum must not auto-register its own migration (which would try
        // to create that table again during `php artisan migrate`).
        Sanctum::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
    }
}
