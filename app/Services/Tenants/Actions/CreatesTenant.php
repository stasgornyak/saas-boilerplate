<?php

namespace App\Services\Tenants\Actions;

use App\Models\License;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Services\_Common\Database\DatabaseService;
use App\Services\_Common\Traits\HasValidation;
use App\Services\_Common\Traits\ReporterTrait;
use App\Services\Avatars\Avatar;
use App\Services\Licenses\Actions\ActivatesLicense;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class CreatesTenant
{
    use HasValidation, ReporterTrait;

    private const LICENSE_PERIOD_IN_DAYS = 14;

    private Tenant $tenant;

    private array $created = [
        'tenant' => false,
        'db' => false,
        'db_user' => false,
    ];

    public function __construct(private readonly array $data, private $user) {}

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function __invoke(): Tenant
    {
        $this->addReport('Starting tenant creation.');

        try {
            $this->createTenant();

            $this->createTenantDatabase();
            $this->setupTenantDatabase();
        } catch (\Exception $e) {
            $this->addReport(
                'Error: '.slugToText($e->getMessage(), false).' in '.$e->getFile().' ('.$e->getLine().')'
            );
            $this->rollbackCreation();

            throw $e;
        }

        $this->addReport('Done!');

        return $this->tenant;
    }

    private function createTenant(): void
    {
        $tenantSlug = $this->generateTenantSlug();

        $dbName = config('tenancy.database.prefix').$tenantSlug;
        // Backslash is not allowed in db password
        $dbPassword = str_replace('\\', '/', Str::password(DB_PASSWORD_LENGTH));

        $tenantData = [
            'slug' => $tenantSlug,
            'tenancy_db_name' => $dbName,
            'tenancy_db_username' => $dbName,
            'tenancy_db_password' => $dbPassword,
            'name' => $this->data['name'],
            'settings' => $this->data['settings'] ?? null,
        ];

        if (isset($this->data['avatar'])) {
            $tenantData['avatar'] = Avatar::upload($this->data['avatar'])->getFileName();
        }

        $this->tenant = Tenant::create($tenantData);
        $this->tenant->attachUser($this->user, isOwner: true);

        $this->activateLicense();

        $this->addReport('Tenant '.$tenantSlug.' created.');
        $this->created['tenant'] = true;
    }

    private function generateTenantSlug(): string
    {
        do {
            $tenantSlug = strtolower(Str::random(TENANT_HASH_LENGTH));
        } while (Tenant::where('slug', $tenantSlug)->exists());

        return $tenantSlug;
    }

    /**
     * @throws \RuntimeException
     */
    private function createTenantDatabase(): void
    {
        $dbName = $this->tenant->tenancy_db_name;
        $dbUserName = $this->tenant->tenancy_db_username;
        $dbPassword = $this->tenant->tenancy_db_password;

        if (DatabaseService::databaseExists($dbName) || DatabaseService::userExists($dbUserName)) {
            throw new \RuntimeException('Database or database user already exists.');
        }

        if (! DatabaseService::createDatabase($dbName)) {
            throw new \RuntimeException('Creating database error.');
        }

        $this->addReport('Database '.$dbName.' created.');
        $this->created['db'] = true;

        if (! DatabaseService::createUser($dbName, $dbUserName, $dbPassword)) {
            throw new \RuntimeException('Creating database user error.');
        }

        $this->addReport('Database user '.$dbUserName.' created.');
        $this->created['db_user'] = true;
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    private function setupTenantDatabase(): void
    {
        Artisan::call('tenants:migrate', [
            '--tenants' => [$this->tenant->slug],
            '--seed' => true,
            '--seeder' => 'Database\Seeders\Tenant\TenantDatabaseSeeder',
        ]);

        $dbName = $this->tenant->tenancy_db_name;
        $this->addReport('Database '.$dbName.' seeded.');

        tenancy()->initialize($this->tenant->slug);

        $this->createTenantUser();

        tenancy()->end();
    }

    private function createTenantUser(): void
    {
        if (! TenantUser::where('central_id', $this->user->id)->exists()) {
            $tenantUser = TenantUser::create([
                'central_id' => $this->user->id,
            ]);

            $role = Role::where('code', 'admin')->where('guard_name', 'tenant_api')->first();
            $tenantUser->assignRole($role->id);

            $this->addReport('Tenant user '.$tenantUser->id.' created.');
        }

    }

    private function rollbackCreation(): void
    {
        if (tenancy()->initialized) {
            tenancy()->end();
        }

        $dbName = $this->tenant->tenancy_db_name;
        $dbUserName = $this->tenant->tenancy_db_username;

        if ($this->created['db_user']) {
            DatabaseService::deleteUser($dbUserName);
        }

        if ($this->created['db']) {
            DatabaseService::deleteDatabase($dbName);
        }

        if ($this->created['tenant']) {
            $this->tenant->usersWithInactive()->detach();
            $this->tenant->delete();
        }

    }

    private function activateLicense(): void
    {
        $license = License::create([
            'tenant_id' => $this->tenant->id,
        ]);

        (new ActivatesLicense(license: $license, period: self::LICENSE_PERIOD_IN_DAYS))();
    }
}
