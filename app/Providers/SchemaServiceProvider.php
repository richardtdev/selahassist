<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class SchemaServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Get the schema from config
        $schema = Config::get('database.connections.pgsql.schema', 'public');
        
        // Set the search path to include our schema
        if ($schema !== 'public') {
            DB::statement("SET search_path TO {$schema}, public");
        }
    }
}
