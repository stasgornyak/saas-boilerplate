<?php

namespace App\Http\Requests\TenantUsers;

use App\Http\Requests\ApiFormRequest;

class StoreTenantUserRequest extends ApiFormRequest
{
    public function filters(): array
    {
        return [
            'email' => 'lowercase',
        ];
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:100'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ];
    }
}
