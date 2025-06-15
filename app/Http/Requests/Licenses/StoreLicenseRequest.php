<?php

namespace App\Http\Requests\Licenses;

use App\Http\Requests\ApiFormRequest;
use App\Models\Tenant;
use Illuminate\Validation\Rule;

class StoreLicenseRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'tenant_id' => [
                'required',
                'integer',
                Rule::exists('tenants', 'id')
                    ->where(function ($query) {
                        (new Tenant)->scopeWhereUserIsActive($query, $this->user());
                    }),
            ],
            'tariff_id' => [
                'required',
                'integer',
                Rule::exists('tariffs', 'id')
                    ->where('is_active', true),
            ],
        ];
    }
}
