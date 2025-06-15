<?php

use App\Http\Controllers\API\v1;
use App\Http\Middleware;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;

Route::prefix('api/v1')->group(function () {
    Route::prefix('users')->group(function () {
        Route::withoutMiddleware([Middleware\VerifyJWT::class])->group(function () {
            Route::post('/register', [v1\UserController::class, 'register']);
            Route::post('login', [v1\UserController::class, 'login']);
            Route::patch('/password', [v1\UserController::class, 'resetPassword']);
        });

        Route::post('/refresh', [v1\UserController::class, 'refresh'])->name('api.refresh');
        Route::post('/logout', [v1\UserController::class, 'logout']);
        Route::get('/current', [v1\UserController::class, 'getCurrentUser']);
        Route::post('/current', [v1\UserController::class, 'updateCurrentUser']);
        Route::patch('/current/password', [v1\UserController::class, 'changeCurrentUserPassword']);
    });

    Route::prefix('tenants')->group(function () {
        Route::get('/', [v1\TenantController::class, 'index']);
        Route::get('/{id}', [v1\TenantController::class, 'show']);
        Route::post('/', [v1\TenantController::class, 'store']);
        Route::post('/{id}', [v1\TenantController::class, 'update']);
        Route::delete('/{id}', [v1\TenantController::class, 'destroy']);
        Route::patch('/sort', [v1\TenantController::class, 'sort']);
    });

    Route::prefix('licenses')->group(function () {
        Route::get('/tariffs', [v1\LicenseController::class, 'getTariffs']);
        Route::get('/', [v1\LicenseController::class, 'index']);
        Route::get('/{id}', [v1\LicenseController::class, 'show']);
        Route::post('/', [v1\LicenseController::class, 'store']);
    });

    Route::prefix('payments')->group(function () {
        Route::get('/', [v1\PaymentController::class, 'index']);
        Route::get('/{id}', [v1\PaymentController::class, 'show']);
        Route::post('/', [v1\PaymentController::class, 'store']);
        Route::patch('/{id}/checkout', [v1\PaymentController::class, 'getCheckoutUrl']);
        Route::patch('/{id}/status', [v1\PaymentController::class, 'checkPaymentStatus']);
        Route::post('/callback', [v1\PaymentController::class, 'handleCallback'])
            ->withoutMiddleware([Middleware\ConvertCase::class, Middleware\VerifyJWT::class])
            ->name('payments.callback');
    });
});

Route::prefix('{tenant}/api/v1')
    ->middleware([
        InitializeTenancyByPath::class,
        Middleware\SaveActivityDate::class,
    ])
    ->group(function () {
        Route::prefix('users')
            ->middleware('permission:settings,tenant_api')
            ->group(function () {
                Route::get('/current', [v1\TenantUserController::class, 'current']);
                Route::get('/', [v1\TenantUserController::class, 'index']);
                Route::get('/{id}', [v1\TenantUserController::class, 'show']);

                Route::middleware('license')->group(function () {
                    Route::post('/', [v1\TenantUserController::class, 'store']);
                    Route::put('/{id}', [v1\TenantUserController::class, 'update']);
                    Route::delete('/{id}', [v1\TenantUserController::class, 'destroy']);
                });
            });

        Route::prefix('roles')
            ->middleware('permission:settings,tenant_api')
            ->group(function () {
                Route::get('/permissions', [v1\RoleController::class, 'permissions']);
                Route::get('/', [v1\RoleController::class, 'index']);
                Route::get('/{id}', [v1\RoleController::class, 'show']);

                Route::middleware('license')->group(function () {
                    Route::post('/', [v1\RoleController::class, 'store']);
                    Route::put('/{id}', [v1\RoleController::class, 'update']);
                    Route::delete('/{id}', [v1\RoleController::class, 'destroy']);
                    Route::patch('/sort', [v1\RoleController::class, 'sort']);
                });
            });

    });
