<?php

namespace App\Http\Requests\Roles;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class SortRoleRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where('guard_name', 'tenant_api'),
            ],
        ];
    }
}
