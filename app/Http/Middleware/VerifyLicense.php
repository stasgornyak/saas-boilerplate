<?php

namespace App\Http\Middleware;

use App\Exceptions\ForbiddenException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyLicense
{
    /**
     * Handle an incoming request.
     *
     * @throws ForbiddenException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenancy()->tenant;

        if (! $tenant) {
            throw new \RuntimeException('Tenant not set.');
        }

        $license = $tenant->activeLicenses()->first();

        if (! $license) {
            throw new ForbiddenException(
                message: MESSAGES['prohibited_by_license'],
                description: DESCRIPTIONS['prohibited_by_license'],
            );
        }

        return $next($request);
    }
}
