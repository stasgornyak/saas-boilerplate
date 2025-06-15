<?php

namespace App\Services\Users;

use App\Models\TenantUser;
use App\Services\Users\Actions\AddsUserToTenant;
use App\Services\Users\Actions\RemovesUserFromTenant;
use App\Services\Users\Actions\UpdatesUserInTenant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TenantUserService
{
    public function index(): Collection
    {
        $users = TenantUser::with('roles')->get();

        $centralUsers = tenancy()->tenant
            ->usersWithInactive()
            ->find($users->pluck('central_id'));

        return $this->combineUsers($users, $centralUsers);
    }

    public function show(int $id): TenantUser
    {
        $user = TenantUser::with('roles')->findOrfail($id);

        $user->central = tenancy()->tenant
            ->usersWithInactive()
            ->find($user->central_id);

        return $user;
    }

    public function store(array $data): TenantUser
    {
        $tenant = tenancy()->tenant;

        return DB::transaction(function () use ($data, $tenant) {
            return (new AddsUserToTenant($data, $tenant))();
        });
    }

    public function update(array $data, int $id): TenantUser
    {
        $user = TenantUser::with('roles')->findOrFail($id);
        $tenant = tenancy()->tenant;

        return DB::transaction(function () use ($data, $tenant, $user) {
            return (new UpdatesUserInTenant($user, $data, $tenant))();
        });
    }

    public function current(): TenantUser
    {
        $user = auth()->tenantUser();
        $user->load('roles.permissions');

        $user->central = tenancy()->tenant
            ->usersWithInactive()
            ->find($user->central_id);

        return $user;
    }

    public function destroy(int $id): array
    {
        $user = TenantUser::findOrFail($id);
        $tenant = tenancy()->tenant;

        DB::transaction(function () use ($user, $tenant) {
            (new RemovesUserFromTenant($tenant, $user))();
        });

        return ['id' => $id];
    }

    private function combineUsers(Collection $users, Collection $centralUsers): Collection
    {
        return $users
            ->map(function ($user) use ($centralUsers) {
                $centralUser = $centralUsers->firstWhere('id', $user->central_id);

                if (! $centralUser) {
                    return null;
                }

                $user->central = $centralUser;

                return $user;
            })
            ->filter()
            ->values();
    }
}
