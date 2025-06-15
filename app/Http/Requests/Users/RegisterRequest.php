<?php

namespace App\Http\Requests\Users;

use App\Enums\Language;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends ApiFormRequest
{
    public function filters(): array
    {
        return [
            'first_name' => 'capitalize',
            'last_name' => 'capitalize',
            'email' => 'lowercase',
            'language' => 'lowercase',
        ];
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['string', 'max:100'],
            'language' => ['nullable', 'string', Rule::enum(Language::class)],
        ];
    }
}
