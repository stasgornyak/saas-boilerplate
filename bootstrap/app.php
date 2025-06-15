<?php

use App\Exceptions as AppExceptions;
use App\Http\Middleware as AppMiddleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Stancl\Tenancy\Contracts\TenantCouldNotBeIdentifiedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->group(base_path('routes/api/v1.php'));
            Route::middleware('api')
                ->withoutMiddleware(AppMiddleware\VerifyJWT::class)
                ->group(base_path('routes/api/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(
            append: [
                AppMiddleware\ConvertCase::class,
                AppMiddleware\VerifyJWT::class,
            ],
        );

        $middleware->alias([
            'permission' => PermissionMiddleware::class,
            'license' => AppMiddleware\VerifyLicense::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReport([
            AppExceptions\ForbiddenException::class,
            AppExceptions\LogicException::class,
            AppExceptions\NotAuthenticatedException::class,
            AppExceptions\ValidationException::class,
            ModelNotFoundException::class,
            TenantCouldNotBeIdentifiedException::class,
        ]);

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($e->getPrevious() instanceof ModelNotFoundException) {
                $resourceName = Str::of($e->getPrevious()->getModel())->classBasename()->snake()->toString();
                $resourceName = $resourceName === 'tenant_user' ? 'user' : $resourceName;
                $resourceNameForDescription = Str::of($resourceName)->replace('_', ' ')->ucfirst();

                $responseData = [
                    'message' => $resourceName.'_'.MESSAGES['not_found'],
                    'description' => $resourceNameForDescription.' '.DESCRIPTIONS['not_found'],
                    'data' => null,
                ];

                return response()->json($responseData, Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof TenantCouldNotBeIdentifiedException) {
                $responseData = [
                    'message' => MESSAGES['tenant_could_not_be_identified'],
                    'description' => $e->getMessage(),
                    'data' => null,
                ];

                return response()->json($responseData, Response::HTTP_FORBIDDEN);
            }

            if ($e instanceof UnauthorizedException) {
                throw new AppExceptions\ForbiddenException(description: $e->getMessage());
            }

            if ($request->isJson()) {
                if (isLocalEnvironment() || app()->environment(['test', 'testing'])) {
                    $responseData = [
                        'message' => MESSAGES['server_error'],
                        'description' => 'Message: '.$e->getMessage().', File: '.$e->getFile().', Line: '.$e->getLine(),
                        'data' => $e->getTrace(),
                    ];

                    return response()->json($responseData, Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                throw new AppExceptions\ServerErrorException(description: $e->getMessage());
            }

            return false;
        });
    })->create();
