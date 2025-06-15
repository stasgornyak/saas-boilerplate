<?php

namespace App\Mixins;

use App\Models\TenantUser;

class JWTGuardMixin
{
    public function tenantUser(): \Closure
    {
        return function () {
            try {
                $payload = $this->payload();
            } catch (\Exception $e) {
                return null;
            }

            if (! $payload) {
                return null;
            }

            $centralUserId = $payload->get('sub');

            return TenantUser::where('central_id', $centralUserId)->first();
        };
    }

    public function tenantUserId(): \Closure
    {
        return function () {
            try {
                $payload = $this->payload();
            } catch (\Exception $e) {
                return null;
            }

            if (! $payload) {
                return null;
            }

            $centralUserId = $payload->get('sub');

            return TenantUser::where('central_id', $centralUserId)->value('id');
        };
    }
}
