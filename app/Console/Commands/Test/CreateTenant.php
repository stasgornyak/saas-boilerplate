<?php

namespace App\Console\Commands\Test;

use App\Models\Tenant;
use App\Services\_Common\Database\DatabaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * This command is used to run tests locally
 */
class CreateTenant extends Command
{
    public const TEST_TENANT_SLUG = 'instance';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create-tenant {slug?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test tenant.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->warn('ATTENTION! This command refresh database!');

        if (! $this->confirm('Do you wish to continue?')) {
            $this->info('Test tenant is not created.');

            return self::SUCCESS;
        }

        $slug = $this->argument('slug') ?: self::TEST_TENANT_SLUG;

        try {
            $this->ensureEnvironmentIsTest();
            $this->refreshDatabase();
            $tenant = $this->getTenant($slug);

            $this->createTenantDatabase($tenant);
            $tenant = $this->setTenantSettings($tenant);
            $this->seedTenantDatabase($tenant);
        } catch (\Exception $e) {
            $message = $e instanceof \RuntimeException
                ? $e->getMessage()
                : $e->getMessage().' in '.$e->getFile().' ('.$e->getLine().')';

            $this->error($message);

            return self::FAILURE;
        }

        $this->info('Test tenant created.');

        return self::SUCCESS;
    }

    private function ensureEnvironmentIsTest(): void
    {
        if (! app()->environment(['test', 'testing'])) {
            throw new \RuntimeException('Environment must be \'test\'!');
        }

    }

    private function refreshDatabase(): void
    {
        Artisan::call('migrate:fresh --seed');
    }

    private function getTenant(string $slug): Tenant
    {
        if (! ($tenant = Tenant::where('slug', $slug)->first())) {
            throw new \RuntimeException('Test tenant not found.');
        }

        if (! $tenant->tenancy_db_name || ! $tenant->tenancy_db_username) {
            throw new \RuntimeException('DB name or username not set.');
        }

        return $tenant;
    }

    private function createTenantDatabase(Tenant $tenant): void
    {
        $dbName = $tenant->tenancy_db_name;
        $dbUserName = $tenant->tenancy_db_username;
        $dbPassword = $tenant->tenancy_db_password;

        if (DatabaseService::databaseExists($dbName) || DatabaseService::userExists($dbUserName)) {
            throw new \RuntimeException('Database or database user already exists.');
        }

        if (! DatabaseService::createDatabase($dbName)) {
            throw new \RuntimeException('Creating database error.');
        }

        if (! DatabaseService::createUser($dbName, $dbUserName, $dbPassword)) {
            throw new \RuntimeException('Creating database user error.');
        }

    }

    private function setTenantSettings(Tenant $tenant): Tenant
    {
        // Add logic if needed
        return $tenant;
    }

    private function seedTenantDatabase(Tenant $tenant): void
    {
        Artisan::call('tenants:migrate-fresh', ['--tenants' => [$tenant->slug]]);
        Artisan::call('tenants:seed', ['--tenants' => [$tenant->slug]]);
    }
}
