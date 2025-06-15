<?php

namespace App\Http\Requests\Tenants;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class SortTenantsRequest extends ApiFormRequest
{
    public function rules(): array
    {
        $tenantIds = auth()
            ->user()
            ->tenants()
            ->pluck('id');

        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', Rule::in($tenantIds), 'distinct'],
        ];
    }
}
