<?php

namespace Database\Seeders\e2e;

use App\Enums\LicenseStatus;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Rename to 'TenantSeeder' (in Fine and ci-Common)
 */
class TenantSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'tenant_data' => [
                    'slug' => 'instance',
                    'name' => 'Instance',
                    'tenancy_db_name' => 'saas-boilerplate-instance',
                    'tenancy_db_password' => 'saas-boilerplate-instance',
                    'tenancy_db_username' => 'saas-boilerplate-instance',
                ],
                'license_data' => [
                    'valid_from' => now(),
                    'valid_to' => now()->addYear(),
                    'status' => LicenseStatus::Active,
                ],
                'users_data' => [
                    [
                        'user_data' => [
                            'email' => 'user_one@e2e.example.com',
                            'password' => '11111111',
                            'language' => 'uk',
                            'first_name' => 'UserOne',
                            'last_name' => 'E2EExample',
                        ],

                        'pivot_data' => [
                            'is_owner' => true,
                            'sort' => 10,
                        ],
                    ], [
                        'user_data' => [
                            'email' => 'user_two@e2e.example.com',
                            'password' => '11111111',
                            'language' => 'en',
                            'first_name' => 'UserTwo',
                            'last_name' => 'E2EExample',
                        ],

                        'pivot_data' => [
                            'is_owner' => false,
                            'sort' => 20,
                        ],
                    ],
                ],
            ],
        ];

        foreach ($data as $dataItem) {
            $tenant = Tenant::updateOrCreate(
                ['slug' => $dataItem['tenant_data']['slug']],
                $dataItem['tenant_data'],
            );

            $tenant->licenses()->create($dataItem['license_data']);

            foreach ($dataItem['users_data'] as $userData) {
                $user = User::updateOrCreate(
                    ['email' => $userData['user_data']['email']],
                    $userData['user_data'],
                );

                $tenant->users()->attach($user->id, $userData['pivot_data']);
            }

        }

    }
}
