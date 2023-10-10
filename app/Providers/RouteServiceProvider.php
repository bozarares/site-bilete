<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Vinkla\Hashids\Facades\Hashids;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        Route::bind('event', function ($value) {
            $hashids = Hashids::connection('events');
            $decodedId = $hashids->decode($value)[0] ?? null;
            return \App\Models\Event::findOrFail($decodedId);
        });
        Route::bind('staff', function ($value) {
            $hashids = Hashids::connection('staff');
            $decodedId = $hashids->decode($value)[0] ?? null;
            return \App\Models\Staff::findOrFail($decodedId);
        });
        Route::bind('ticket_category', function ($value) {
            $hashids = Hashids::connection('ticket_category');
            $decodedId = $hashids->decode($value)[0] ?? null;
            return \App\Models\TicketCategory::findOrFail($decodedId);
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
