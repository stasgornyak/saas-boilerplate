<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\ApiFormRequest;

class ChangeCurrentUserPasswordRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'password_current' => ['required', 'string'],
            'password_new' => ['required', 'string', 'between:8,50'],
        ];
    }
}
