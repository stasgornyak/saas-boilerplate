<?php

namespace App\Console\Commands\Test;

use App\Models\Tenant;
use App\Services\_Common\Database\DatabaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * This command is used to delete tests tenant locally
 */
class DeleteTenant extends Command
{
    public const TEST_TENANT_SLUG = 'instance';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:delete-tenant {slug?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete test tenant.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->warn('ATTENTION! This command refresh database!');

        if (! $this->confirm('Do you wish to continue?')) {
            $this->info('Test tenant is not deleted.');

            return self::SUCCESS;
        }

        $slug = $this->argument('slug') ?: self::TEST_TENANT_SLUG;

        try {
            $this->ensureEnvironmentIsTest();
            $tenant = $this->getTenant($slug);

            $this->deleteDatabase($tenant);
            $this->refreshDatabase();
        } catch (\Exception $e) {
            $this->error($e->getMessage().' in '.$e->getFile().' ('.$e->getLine().')');

            return self::FAILURE;
        }

        $this->info('Test tenant deleted.');

        return self::SUCCESS;
    }

    private function ensureEnvironmentIsTest(): void
    {
        if (! app()->environment(['test', 'testing'])) {
            throw new \RuntimeException('Environment must be \'test\'!');
        }

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

    private function deleteDatabase(Tenant $tenant): void
    {
        $dbName = $tenant->tenancy_db_name;
        $dbUserName = $tenant->tenancy_db_username;

        if (DatabaseService::databaseExists($dbName)) {
            if (! DatabaseService::deleteDatabase($dbName)) {
                throw new \RuntimeException('Deleting database error.');
            }

            if (! DatabaseService::deleteUser($dbUserName)) {
                throw new \RuntimeException('Deleting database user error.');
            }

        }

    }

    private function refreshDatabase(): void
    {
        Artisan::call('migrate:fresh --seed');
    }
}
