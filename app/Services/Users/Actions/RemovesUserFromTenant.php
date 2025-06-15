<?php

namespace App\Services\Users\Actions;

use App\Exceptions\LogicException;
use App\Models\Tenant;
use Illuminate\Contracts\Auth\Authenticatable;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class RemovesUserFromTenant
{
    public function __construct(private Tenant $tenant, private Authenticatable $user) {}

    /**
     * @throws LogicException
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function __invoke(): void
    {
        $this->ensureUserIsNotOwner($this->user, $this->tenant);
        $this->removeUserFromTenant($this->user, $this->tenant);
    }

    /**
     * @throws LogicException
     */
    private function ensureUserIsNotOwner(Authenticatable $user, Tenant $tenant): void
    {
        $isOwner = $tenant
            ->users()
            ->where('user_id', $user->central_id)
            ->value('is_owner');

        if ($isOwner) {
            throw new LogicException('owner_user_can_not_be_removed');
        }

    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    private function removeUserFromTenant(Authenticatable $user, Tenant $tenant): void
    {
        $tenant->users()->detach($user->central_id);

        $user->delete();
    }
}
