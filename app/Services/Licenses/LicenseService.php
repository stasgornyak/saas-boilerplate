<?php

namespace App\Services\Licenses;

use App\Enums\LicenseStatus;
use App\Exceptions\ForbiddenException;
use App\Models\License;
use App\Models\Tariff;
use App\Models\Tenant;
use App\Services\Licenses\Actions\ActivatesLicense;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LicenseService
{
    public function getTariffs(): Collection
    {
        return Tariff::active()->get()
            ->map(function ($tariff) {
                return [
                    'id' => $tariff->id,
                    'period' => $tariff->period->value,
                    'price' => $tariff->price,
                    'currency' => $tariff->currency->toArray(),
                ];
            })
            ->values();
    }

    public function index(array $data): LengthAwarePaginator
    {
        $filters = $data['filters'] ?? [];
        $pagination = $data['pagination'];

        return License::query()
            ->permittedForUser(auth()->user())
            ->whereFilters($filters)
            ->latest()
            ->paginate($pagination['per_page'], ['*'], null, $pagination['page']);
    }

    public function show(int $id): License
    {
        return License::query()
            ->permittedForUser(auth()->user())
            ->with(['payments'])
            ->findOrFail($id);
    }

    /**
     * @throws ForbiddenException
     */
    public function store(array $data): License
    {
        $this->ensureUserCanChooseLicense($data['tenant_id']);

        return License::create($data)->refresh();
    }

    public function handlePaymentApproval(License $license): License
    {
        if ($license->status !== LicenseStatus::Created) {
            return $license;
        }

        $activeLicense = License::query()
            ->where('tenant_id', $license->tenant_id)
            ->whereStatus(LicenseStatus::Active)
            ->where('id', '!=', $license->id)
            ->first();

        if (! $activeLicense) {
            (new ActivatesLicense($license))();
        } else {
            if ($activeLicense->isExpired()) {
                (new ActivatesLicense($license))();
                $activeLicense->update(['status' => LicenseStatus::Expired]);
            } else {
                (new ActivatesLicense($license, $activeLicense->valid_to->copy()->addSecond()))();
            }

        }

        return $license->refresh();
    }

    /**
     * TODO: transfer to Gate or Policy?
     *
     * @throws ForbiddenException
     */
    private function ensureUserCanChooseLicense(int $tenantId): void
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = auth()->user();

        if ($user->isOwner($tenant)) {
            return;
        }

        $role = $user->getRoleInTenant($tenant);

        if ($role && $role->code === 'admin') {
            return;
        }

        throw new ForbiddenException('admin_only_can_choose_license');
    }
}
