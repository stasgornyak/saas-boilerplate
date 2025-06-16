<?php

namespace Database\Seeders\Tenant;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionsData = config('role-permission.permissions');

        foreach ($permissionsData as $permissionData) {
            Permission::firstOrCreate(Arr::only($permissionData, ['name', 'guard_name']));
        }

        $rolesData = config('role-permission.roles');

        if (! Role::exists()) {
            foreach ($rolesData as $roleData) {
                $role = Role::create(Arr::only($roleData, ['name', 'code', 'is_system', 'guard_name']));

                if (is_array($roleData['permissions'])) {
                    $rolePermissionIds = Permission::whereGuardName($roleData['guard_name'])
                        ->whereIn('name', $roleData['permissions'])->pluck('id');
                } elseif ($roleData['permissions'] === 'all') {
                    $rolePermissionIds = Permission::whereGuardName($roleData['guard_name'])
                        ->pluck('id');
                } else {
                    $rolePermissionIds = collect();
                }

                $role->syncPermissions($rolePermissionIds);
            }

        }

        // Permissions seeded for system roles
        foreach (Role::system()->get() as $systemRole) {
            $roleData = collect($rolesData)->firstWhere('code', $systemRole->code);

            if ($roleData) {
                $rolePermissions = Permission::whereGuardName($roleData['guard_name'])
                    ->when(is_array($roleData['permissions']), function ($query) use ($roleData) {
                        $query->whereIn('name', $roleData['permissions']);
                    })
                    ->pluck('id');

                $systemRole->syncPermissions($rolePermissions);
            }

        }

    }
}
