<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        // Check if the application is running in the local environment
        if (App::environment('local')) {
            // Register a listener for database queries
            DB::listen(function ($query) {
                // Check if debugging is enabled
                if (config('app.debug')) {
                    // Generate the SQL query string
                    $sql = Str::replaceArray('?', $query->bindings, $query->sql);
                    // Log the query to a specific channel named 'database'
                    \Log::channel('single')->debug($sql);
                }
            });
        }
    }
}
