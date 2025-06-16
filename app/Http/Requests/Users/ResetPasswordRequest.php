<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class ResetPasswordRequest extends ApiFormRequest
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
            'email' => [
                'required',
                'string',
                Rule::exists('users', 'email'),
            ],
        ];
    }
}
