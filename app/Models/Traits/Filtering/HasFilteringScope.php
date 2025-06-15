<?php

namespace App\Models\Traits\Filtering;

use App\Services\_Common\Filtration\FilterGenerator;
use Illuminate\Database\Eloquent\Builder;

trait HasFilteringScope
{
    abstract public static function getFilterParams(): array;

    public function scopeWhereFilters(Builder $query, array $filters, bool $fullColumnName = false): Builder
    {
        return FilterGenerator::addFilters($query, $filters, $fullColumnName);
    }

}
