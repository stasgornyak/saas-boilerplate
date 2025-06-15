<?php

namespace App\Providers;

use App\Mixins\JWTGuardMixin;
use Illuminate\Support\ServiceProvider;
use Tymon\JWTAuth\JWTGuard;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register() {}

    /**
     * Bootstrap services.
     *
     * @throws \ReflectionException
     */
    public function boot(): void
    {
        if (class_exists(JWTGuard::class)) {
            JWTGuard::mixin(new JWTGuardMixin);
        }

    }
}
