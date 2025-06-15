<?php

namespace App\Http\Requests\TenantUsers;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantUserRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'is_active' => ['boolean'],
            'role_id' => ['required', 'sometimes', 'integer', Rule::exists('roles', 'id')],
        ];
    }
}
