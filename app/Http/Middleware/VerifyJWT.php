<?php

namespace App\Http\Middleware;

use App\Exceptions\ForbiddenException;
use App\Exceptions\NotAuthenticatedException;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class VerifyJWT
{
    /**
     * Handle an incoming request.
     *
     * @throws NotAuthenticatedException|ForbiddenException
     */
    public function handle(Request $request, \Closure $next): mixed
    {
        $this->checkForToken($request);

        try {
            $user = $this->authenticate();
            $this->checkForTenant($user);
        } catch (JWTException $e) {
            if ($e instanceof TokenExpiredException) {
                $isRefreshAllowed = $this->isRefreshAllowed($request);

                if ($isRefreshAllowed) {
                    if ($request->routeIs('api.refresh')) {
                        return $next($request);
                    }

                    try {
                        $newToken = $this->refresh();

                        if (! $newToken) {
                            throw new NotAuthenticatedException(description: 'Can not refresh token.');
                        }

                    } catch (JWTException $e) {
                        throw new NotAuthenticatedException(description: $e->getMessage());
                    }

                    $request = $this->setAuthHeaderToRequest($request, $newToken);

                    $response = $next($request);

                    return $this->setAuthHeaderToResponse($response, $newToken);
                }

            }

            throw new NotAuthenticatedException(description: $e->getMessage());
        }

        return $next($request);
    }

    /**
     * @throws NotAuthenticatedException
     */
    protected function checkForToken(Request $request): void
    {
        if (
            ! auth()
                ->parser()
                ->setRequest($request)
                ->hasToken()
        ) {
            throw new NotAuthenticatedException(description: 'Token not provided.');
        }

    }

    /**
     * @return JWTSubject
     *
     * @throws JWTException
     * @throws NotAuthenticatedException
     */
    protected function authenticate(): Authenticatable
    {
        auth()
            ->parseToken()
            ->checkOrFail();

        if (! ($user = auth()->user())) {
            auth()->invalidate(true);

            throw new NotAuthenticatedException(description: 'User not authenticated.');
        }

        return $user;
    }

    protected function isRefreshAllowed(Request $request): bool
    {
        $token = auth()
            ->parser()
            ->setRequest($request)
            ->parseToken();
        $payload = auth()
            ->manager()
            ->getJWTProvider()
            ->decode($token);

        $isRefreshAllowed = (bool) $payload['rfs'] === true;
        $lastRefreshAt = (int) $payload['nbf'];
        $refreshAllowedBefore = $lastRefreshAt + config('jwt.refresh_relative_ttl') * SECONDS_IN_MINUTE;
        $isRefreshInTime = now()->timestamp <= $refreshAllowedBefore;

        return $isRefreshAllowed && $isRefreshInTime;
    }

    protected function refresh(): string
    {
        return auth()->refresh(true);
    }

    protected function setAuthHeaderToRequest(Request $request, $token): Request
    {
        $request->headers->set('Authorization', 'Bearer '.$token);
        auth()
            ->setToken($token)
            ->user();

        return $request;
    }

    protected function setAuthHeaderToResponse($response, $token): mixed
    {
        $payload = auth()
            ->setToken($token)
            ->payload()
            ->toArray();
        $expires = $payload['exp'] ?? null;

        $response->headers->set('Refresh-Token', $token);
        $response->headers->set('Refresh-Expires', $expires);
        $response->headers->set('Access-Control-Expose-Headers', '*');

        return $response;
    }

    /**
     * @throws ForbiddenException
     */
    protected function checkForTenant(User $user): void
    {
        if (! (tenancy()->tenant)) {
            return;
        }

        if (! tenancy()->tenant->hasUser($user)) {
            throw new ForbiddenException;
        }

    }
}
