<?php

namespace App\Services\Users\Actions;

use App\Exceptions\LogicException;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Support\Arr;

class UpdatesUserInTenant
{
    public function __construct(private TenantUser $user, private array $data, private Tenant $tenant) {}

    /**
     * @throws LogicException
     */
    public function __invoke(): TenantUser
    {
        $centralUser = $this->updateCentralUser();
        $this->updateUser();
        $this->user->central = $centralUser;

        return $this->user->refresh();
    }

    /**
     * @throws LogicException
     */
    private function updateCentralUser(): User
    {
        $centralUser = $this->tenant
            ->usersWithInactive()
            ->where('user_id', $this->user->central_id)
            ->firstOrFail();

        if ($centralUser->isOwner($this->tenant)) {
            throw new LogicException('can_not_update_owner');
        }

        if (isset($this->data['is_active'])) {
            $centralUser
                ->tenantsWithInactive()
                ->updateExistingPivot($this->tenant->id, Arr::only($this->data, 'is_active'));
        }

        return $centralUser->refresh();
    }

    private function updateUser(): void
    {
        $userData = Arr::except($this->data, ['role_id']);
        $this->user->update($userData);

        if (isset($this->data['role_id'])) {
            $this->user->syncRoles([$this->data['role_id']]);
        }

        $this->user->refresh();
    }
}
