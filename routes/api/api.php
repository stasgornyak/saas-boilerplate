<?php

use App\Http\Controllers\API;
use App\Http\Middleware\EnsureTestEnvironment;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::post('/e2e/reset-db', [API\E2EController::class, 'resetDB'])
        ->middleware(EnsureTestEnvironment::class);
});
