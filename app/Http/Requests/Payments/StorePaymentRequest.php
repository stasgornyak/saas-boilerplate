<?php

namespace App\Http\Requests\Payments;

use App\Http\Requests\ApiFormRequest;
use App\Models\License;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'license_id' => [
                'required',
                'integer',
                Rule::exists('licenses', 'id')
                    ->where(function ($query) {
                        (new License)->scopePermittedForUser($query, $this->user());
                    }),
            ],
            'description' => ['nullable', 'string'],
        ];
    }
}
