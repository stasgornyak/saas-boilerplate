<?php

namespace Database\Seeders\Tenant\e2e;

use App\Models\Role;
use App\Models\TenantUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class TenantUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersData = [
            [
                'central_id' => 1,
                'role_code' => 'admin',
            ], [
                'central_id' => 2,
                'role_code' => 'staff',
            ],
        ];

        foreach ($usersData as $userData) {
            $user = TenantUser::firstOrCreate(
                Arr::only($userData, 'central_id'),
                Arr::except($userData, 'role_code'),
            );

            $role = Role::firstWhere('code', $userData['role_code']);
            $user->assignRole($role);
        }

    }
}
