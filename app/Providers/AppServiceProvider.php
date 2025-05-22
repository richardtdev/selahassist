<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

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
        // Set the PostgreSQL search_path to the desired schema
        DB::connection('pgsql')->statement('SET search_path TO ' . env('DB_SCHEMA', 'public'));
    }
}