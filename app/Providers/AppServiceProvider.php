<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap: define los rate limiters de la API.
     *
     * Mitiga (OWASP A07 Identification & Authentication Failures / A04):
     *  - 'auth'  : limita intentos de login/registro → frena fuerza bruta.
     *  - 'api'   : límite general por usuario o IP → frena abuso/DoS.
     */
    public function boot(): void
    {
        // Límite estricto para endpoints de autenticación: 5 intentos/min por IP.
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Límite general de la API: 60 req/min por usuario autenticado (o IP).
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(60)->by('user:'.$request->user()->id)
                : Limit::perMinute(30)->by('ip:'.$request->ip());
        });
    }
}
