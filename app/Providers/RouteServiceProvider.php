<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use App\Models\Doctor;

class RouteServiceProvider extends ServiceProvider
{
    /**
    * The path to your application's "home" route.
    *
    * @var string
    */
    public const HOME = '/home';

    /**
    * Define your route model bindings, pattern filters, etc.
    */
    public function boot(): void
    {
        Route::bind('doctor', function ($value) {
            return Doctor::find($value) ?? abort(404);
        });
        
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}

