<?php

namespace App\Http\Requests\Roles;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('roles')],
            'permission_ids' => ['array', 'nullable'],
            'permission_ids.*' => [
                'integer',
                Rule::exists('permissions', 'id')->where('guard_name', 'tenant_api'),
            ],
            'sort' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
