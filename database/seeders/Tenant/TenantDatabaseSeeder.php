<?php

namespace Database\Seeders\Tenant;

use Database\Seeders\Tenant\e2e\TenantUserSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
        ]);

        if (app()->environment(['test', 'testing'])) {
            $this->call([
                TenantUserSeeder::class,
            ]);
        }

    }

}
