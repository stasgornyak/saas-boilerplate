<?php

namespace App\Models\Traits\Filtering;

use App\Enums\LicenseStatus;
use Illuminate\Database\Eloquent\Builder;

trait HasLicenseFilteringScope
{
    use HasFilteringScope;

    public static function getFilterParams(): array
    {
        return ['tenant_id', 'status'];
    }

    public function scopeWhereTenantId(Builder $query, int $tenantId, bool $fullColumnName = false): void
    {
        $column = $fullColumnName ? (new self)->getTable().'.tenant_id' : 'tenant_id';

        $query->where($column, $tenantId);
    }

    public function scopeWhereStatus(Builder $query, string|LicenseStatus $status, bool $fullColumnName = false): void
    {
        $column = $fullColumnName ? (new self)->getTable().'.status' : 'status';
        $status = $status instanceof LicenseStatus ? $status->value : $status;

        $query->where($column, $status);
    }
}
