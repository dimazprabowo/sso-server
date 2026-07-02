<?php

namespace App\Providers;

use App\Http\Middleware\CheckAppAccess;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Route::matched(function (\Illuminate\Routing\Events\RouteMatched $event) {
            $route = $event->route;
            if ($route->getName() === 'passport.authorizations.authorize') {
                $route->middleware(CheckAppAccess::class);
            }
        });
    }
}
