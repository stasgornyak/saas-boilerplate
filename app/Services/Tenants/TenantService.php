<?php

namespace App\Services\Tenants;

use App\Exceptions\ForbiddenException;
use App\Exceptions\LogicException;
use App\Exceptions\ValidationException;
use App\Models\Tenant;
use App\Services\Tenants\Actions\CreatesTenant;
use App\Services\Tenants\Actions\DeletesTenant;
use App\Services\Tenants\Actions\UpdatesTenant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class TenantService
{
    public function index(): Collection
    {
        return auth()
            ->user()
            ->tenants()
            ->with('activeLicenses')
            ->get();
    }

    public function show(int $id): Tenant
    {
        return auth()
            ->user()
            ->tenants()
            ->with('activeLicenses')
            ->findOrFail($id);
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function store(array $data): Tenant
    {
        $user = auth()->user();

        $tenantCreator = new CreatesTenant($data, $user);

        try {
            $tenant = $tenantCreator();

            Log::channel('deploy')->info('Tenant created.', ['report' => $tenantCreator->getReportPrintable()]);
        } catch (\Exception $e) {
            Log::channel('deploy')->info('Tenant creation failed.', [
                'report' => $tenantCreator->getReportPrintable(),
            ]);

            throw $e;
        }

        return $tenant;
    }

    /**
     * @throws ForbiddenException
     * @throws ValidationException
     * @throws TenantCouldNotBeIdentifiedById
     * @throws LogicException
     */
    public function update(array $data, int $id): Tenant
    {
        $user = auth()->user();
        $tenant = $user->tenants()->findOrFail($id);

        return (new UpdatesTenant($tenant, $data, $user))();
    }

    /**
     * @throws ForbiddenException
     */
    public function destroy(int $id): array
    {
        $user = auth()->user();
        $tenant = $user->tenants()->findOrFail($id);

        (new DeletesTenant($tenant, $user))();

        return ['id' => $tenant->id];
    }

    public function sort(array $data): void
    {
        $user = auth()->user();

        DB::transaction(function () use ($data, $user) {
            collect($data['ids'])->each(function ($id, $index) use ($user) {
                $sort = ($index + SORT_FIRST_MODIFIER) * SORT_SECOND_MODIFIER;
                $user->tenants()->updateExistingPivot($id, ['sort' => $sort]);
            });
        });
    }
}
