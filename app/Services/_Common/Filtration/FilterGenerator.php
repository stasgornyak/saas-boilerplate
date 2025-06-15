<?php

namespace App\Services\_Common\Filtration;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class FilterGenerator
{
    public static function addFilters(Builder $query, array $filters = [], bool $fullColumnName = false): Builder
    {
        $filters = collect($filters)
            ->mapWithKeys(function ($value, $key) {
                if (is_array($value)) {
                    $value = collect($value)
                        ->filter(function ($v) {
                            return ! is_null($v);
                        })
                        ->toArray();
                }

                return [$key => $value];
            })
            ->filter(function ($value) {
                return ! (is_null($value) || (is_array($value) && count($value) === 0));
            })
            ->toArray();

        if (count($filters) === 0) {
            return $query->whereRaw(true);
        }

        foreach ($filters as $key => $value) {
            $scopeMethod =
                'where'.
                ucfirst(
                    Str::of($key)
                        ->singular()
                        ->studly()
                );

            $query->$scopeMethod($value, $fullColumnName);
        }

        return $query;
    }

    public static function addOrders(Builder $query, array $orders = []): Builder
    {
        if (empty($orders)) {
            return $query->latest();
        }

        foreach ($orders as $key => $value) {
            $scopeMethod = 'orderBy'.ucfirst(Str::studly($key));
            $query->$scopeMethod($value);
        }

        return $query;
    }
}
