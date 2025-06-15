<?php

namespace App\Http\Requests\Payments;

use App\Enums\PaymentStatus;
use App\Http\Requests\ApiFormRequest;
use App\Models\License;
use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Validation\Rule;

class IndexPaymentRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'filters' => ['array:'.implode(',', Payment::getFilterParams())],
            'filters.license_id' => [
                'integer',
                Rule::exists('licenses', 'id')->where(function ($query) {
                    (new License)->scopePermittedForUser($query, $this->user());
                }),
            ],
            'filters.tenant_id' => [
                'integer',
                Rule::exists('tenants', 'id')->where(function ($query) {
                    (new Tenant)->scopeWhereUserIsActive($query, $this->user());
                }),
            ],
            'filters.status' => [Rule::enum(PaymentStatus::class)],
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
