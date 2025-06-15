<?php

namespace App\Http\Requests\Roles;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'sometimes',
                'string',
                'max:100',
                Rule::unique('roles', 'name')->ignore($this->id),
            ],
            'permission_ids' => ['array', 'nullable'],
            'permission_ids.*' => [
                'integer',
                Rule::exists('permissions', 'id')->where('guard_name', 'tenant_api'),
            ],
            'sort' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
