<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $files = glob(app_path('Helpers').'/*.php');

        foreach ($files as $key => $file) {
            require_once $file;
        }

    }
}
