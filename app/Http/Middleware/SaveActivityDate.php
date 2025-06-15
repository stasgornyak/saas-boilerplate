<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SaveActivityDate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user = auth()->tenantUser();

        if (! $user) {
            return $next($request);
        }

        $user->updateQuietly([
            'last_activity_at' => now(),
        ]);

        return $next($request);
    }
}
