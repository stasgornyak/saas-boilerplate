<?php

namespace App\Services\Users\Actions;

use App\Events\UserAddedToTenant;
use App\Exceptions\LogicException;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\TenantUserPivot;
use App\Models\User;
use Illuminate\Support\Arr;

class AddsUserToTenant
{
    private ?string $password = null;

    public function __construct(private array $data, private Tenant $tenant) {}

    /**
     * @throws LogicException
     */
    public function __invoke(): TenantUser
    {
        $centralUser = $this->getCentralUser();
        $user = $this->createUser($centralUser->id);
        $user->central = $centralUser;

        UserAddedToTenant::dispatch($centralUser, $this->tenant, $this->password);

        return $user->refresh();
    }

    /**
     * @throws LogicException
     */
    private function getCentralUser(): User
    {
        $centralUser = User::where('email', $this->data['email'])->first();

        if ($centralUser) {
            $this->ensureUserDoesntExist($centralUser);
            $this->attachCentralUserToTenant($centralUser);

            return $centralUser;
        }

        $centralUser = $this->createCentralUser();
        $this->attachCentralUserToTenant($centralUser);

        return $centralUser;
    }

    private function attachCentralUserToTenant(User $centralUser): void
    {
        $sort = TenantUserPivot::query()
            ->where('user_id', $centralUser->id)
            ->max('sort') + SORT_SECOND_MODIFIER;

        $pivotData = [
            'is_owner' => false,
            'is_active' => true,
            'sort' => $sort,
        ];

        $centralUser->tenants()->attach($this->tenant, $pivotData);
    }

    /**
     * @throws LogicException
     */
    private function ensureUserDoesntExist(User $centralUser): void
    {
        if ($centralUser->is_trashed) {
            throw new LogicException('user_with_this_email_is_deleted');
        }

        $user = TenantUser::withTrashed()
            ->where('central_id', $centralUser->id)
            ->first();

        if ($user) {
            if ($user->is_trashed) {
                throw new LogicException('user_with_this_email_is_deleted');
            }

            if (! $user->is_active) {
                throw new LogicException('user_with_this_email_exists_and_inactive');
            }

            throw new LogicException('user_with_this_email_already_exists');
        }

    }

    private function createCentralUser(): User
    {
        $centralUserData = Arr::only($this->data, ['email']);
        $centralUserData['language'] = config('app.locale');

        $centralUser = User::create($centralUserData);
        $password = $centralUser->setPassword();

        $this->password = $password;

        return $centralUser;
    }

    private function createUser(int $tenantUserId): TenantUser
    {
        $userData = Arr::except($this->data, ['role_id', 'email']);
        $userData['central_id'] = $tenantUserId;

        $user = TenantUser::create($userData);

        if (isset($this->data['role_id'])) {
            $user->syncRoles([$this->data['role_id']]);
        }

        return $user->refresh();
    }
}
