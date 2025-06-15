<?php

namespace App\Http\Requests\Licenses;

use App\Enums\LicenseStatus;
use App\Http\Requests\ApiFormRequest;
use App\Models\License;
use App\Models\Tenant;
use Illuminate\Validation\Rule;

class IndexLicenseRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'filters' => ['array:'.implode(',', License::getFilterParams())],
            'filters.tenant_id' => [
                'integer',
                Rule::exists('tenants', 'id')->where(function ($query) {
                    (new Tenant)->scopeWhereUserIsActive($query, $this->user());
                }),
            ],
            'filters.status' => [Rule::enum(LicenseStatus::class)],
            'pagination' => ['array:page,per_page'],
            'pagination.page' => ['integer', 'gt:0', 'nullable'],
            'pagination.per_page' => ['integer', 'gt:0', 'nullable'],
        ];
    }

    protected function transformAfterValidation($data): array
    {
        return $this->addDefaultPagination($data);
    }
}
