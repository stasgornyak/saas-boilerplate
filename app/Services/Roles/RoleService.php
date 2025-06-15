<?php

namespace App\Services\Roles;

use App\Exceptions\LogicException;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function index(): Collection
    {
        return Role::get();
    }

    public function show(int $id): Role
    {
        return Role::with('permissions')->whereGuardName('tenant_api')->findOrFail($id);
    }

    public function store(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $data['guard_name'] = 'tenant_api';
            $role = Role::create($data);

            if (isset($data['permission_ids'])) {
                $role->syncPermissions($data['permission_ids']);
            }

            return $role->load('permissions');
        });
    }

    /**
     * @throws LogicException
     */
    public function update(array $data, int $id): Role
    {
        $role = Role::with('permissions')->whereGuardName('tenant_api')->findOrFail($id);

        if ($role->is_system && isset($data['permission_ids'])) {
            $currentPermissions = $role->permissions->pluck('id')->toArray();
            $newPermissions = $data['permission_ids'];

            $isPermissionsChanged = count(array_diff($currentPermissions, $newPermissions)) != 0
                || count(array_diff($newPermissions, $currentPermissions)) != 0;

            if ($isPermissionsChanged) {
                throw new LogicException('can_not_modify_permissions_of_system_role');
            }

        }

        return DB::transaction(function () use ($data, $role) {
            $role->update($data);

            if (isset($data['permission_ids'])) {
                $role->syncPermissions($data['permission_ids']);
            }

            return $role->load('permissions');
        });
    }

    /**
     * @throws LogicException
     */
    public function destroy(int $id): array
    {
        $role = Role::with('permissions')->whereGuardName('tenant_api')->findOrFail($id);

        if ($role->is_system) {
            throw new LogicException('can_not_delete_system_role');
        }

        if ($role->users()->withTrashed()->exists()) {
            throw new LogicException('can_not_delete_role_with_users');
        }

        $role->delete();

        return ['id' => $role->id];
    }

    public function sort(array $data): void
    {
        DB::transaction(function () use ($data) {
            collect($data['ids'])->each(function ($id, $index) {
                $sort = ($index + SORT_FIRST_MODIFIER) * SORT_SECOND_MODIFIER;
                Role::findOrFail($id)->update(['sort' => $sort]);
            });
        });
    }

    public function permissions(): Collection
    {
        return Permission::whereGuardName('tenant_api')
            ->orderBy('id')
            ->get()
            ->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ];
            });
    }
}
