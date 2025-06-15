<?php

namespace App\Http\Middleware;

use App\Exceptions\ForbiddenException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTestEnvironment
{
    /**
     * Handle an incoming request.
     *
     * @throws ForbiddenException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment(['test', 'testing'])) {
            return $next($request);
        }

        throw new ForbiddenException;
    }
}
