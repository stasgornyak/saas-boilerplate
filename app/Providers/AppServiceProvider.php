<?php

namespace App\Providers;

use App\Services\Auth\TenantJWTGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        Auth::extend('tenant_jwt', function ($app, $name, array $config) {
            $guard = new TenantJWTGuard(
                $app['tymon.jwt'],
                $app['auth']->createUserProvider($config['provider']),
                $app['request']
            );

            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });

        $this->configureCommands();
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands($this->app->environment(['production']));
    }
}
