<?php

namespace App\Models\Traits\Filtering;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;

trait HasPaymentFilteringScope
{
    use HasFilteringScope;

    public static function getFilterParams(): array
    {
        return ['license_id', 'status', 'tenant_id'];
    }

    public function scopeWhereLicenseId(Builder $query, int $licenseId, bool $fullColumnName = false): void
    {
        $column = $fullColumnName ? (new self())->getTable().'.license_id' : 'license_id';

        $query->where($column, $licenseId);
    }

    public function scopeWhereStatus(Builder $query, string|PaymentStatus $status, bool $fullColumnName = false): void
    {
        $column = $fullColumnName ? (new self())->getTable().'.status' : 'status';
        $status = $status instanceof PaymentStatus ? $status->value : $status;

        $query->where($column, $status);
    }

    public function scopeWhereTenantId(Builder $query, int $tenantId, bool $fullColumnName = false): void
    {
        $query->whereHas('license', function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        });
    }

}
