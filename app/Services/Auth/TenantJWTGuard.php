<?php

namespace App\Services\Auth;

use App\Models\TenantUser;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\JWTGuard;

class TenantJWTGuard extends JWTGuard
{
    public function user(): ?Authenticatable
    {
        $user = Auth::guard('api')->user();

        if ($user && tenancy()->initialized) {
            return TenantUser::where('central_id', $user->id)->first();
        }

        return null;

    }

    public function id()
    {
        $id = Auth::guard('api')->id();

        if ($id && tenancy()->initialized) {
            return TenantUser::where('central_id', $id)->value('id');
        }

        return null;
    }
}
