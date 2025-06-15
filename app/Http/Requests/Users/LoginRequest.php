<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\ApiFormRequest;

class LoginRequest extends ApiFormRequest
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
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember_me' => ['boolean', 'nullable'],
        ];
    }
}
