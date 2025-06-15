<?php

namespace App\Services\Tenants\Actions;

use App\Exceptions\ForbiddenException;
use App\Models\License;
use App\Models\Payment;
use App\Models\Tenant;
use App\Services\_Common\Database\DatabaseService;
use App\Services\_Common\Traits\ReporterTrait;
use Illuminate\Contracts\Auth\Authenticatable;

class DeletesTenant
{
    use ReporterTrait;

    public function __construct(private Tenant $tenant, private Authenticatable $user) {}

    /**
     * @throws ForbiddenException
     */
    public function __invoke(): void
    {
        $this->ensureUserCanDeleteTenant();

        $this->addReport('Starting tenant deletion.');

        try {
            $this->deleteTenantDatabase();

            $this->deleteTenant();
        } catch (\Exception $e) {
            $this->addReport('Error: '.$e->getMessage());

            throw $e;
        }

        $this->addReport('Done!');
    }

    /**
     * @throws ForbiddenException
     */
    private function ensureUserCanDeleteTenant(): void
    {
        if (! $this->user->isOwner($this->tenant)) {
            throw new ForbiddenException('access_denied_for_not_owner');
        }

    }

    /**
     * @throws \RuntimeException
     */
    private function deleteTenantDatabase(): void
    {
        $dbName = $this->tenant->tenancy_db_name;
        $dbUserName = $this->tenant->tenancy_db_username;

        $dbDeleted = DatabaseService::deleteDatabase($dbName);
        $dbUserDeleted = DatabaseService::deleteUser($dbUserName);

        if (! $dbDeleted || ! $dbUserDeleted) {
            throw new \RuntimeException('Deleting database error.');
        }

        $this->addReport('Database and user '.$dbName.' deleted.');
    }

    private function deleteTenant(): void
    {
        $slug = $this->tenant->slug;

        $this->tenant->usersWithInactive()->detach();
        $this->deleteLicenses();
        $this->tenant->delete();

        $this->addReport('Tenant '.$slug.' deleted.');
    }

    private function deleteLicenses(): void
    {
        $licenseIds = $this->tenant->licenses()->pluck('id');

        foreach ($licenseIds as $licenseId) {
            Payment::where('license_id', $licenseId)->delete();
            License::where('id', $licenseId)->delete();
        }

    }
}
