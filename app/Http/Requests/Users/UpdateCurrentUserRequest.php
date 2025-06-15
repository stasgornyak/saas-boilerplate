<?php

namespace App\Http\Requests\Users;

use App\Enums\Language;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UpdateCurrentUserRequest extends ApiFormRequest
{
    public function filters(): array
    {
        return [
            'first_name' => 'capitalize',
            'last_name' => 'capitalize',
            'language' => 'lowercase',
        ];
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'sometimes', 'string', 'max:100'],
            'last_name' => ['string', 'max:100', 'nullable'],
            'language' => ['required', 'sometimes', 'string', Rule::enum(Language::class)],
            'avatar' => [
                'nullable',
                File::image()
                    ->dimensions(
                        Rule::dimensions()
                            ->maxWidth(5000)
                            ->maxHeight(5000)
                    )
                    ->max(5120),
            ],
        ];
    }
}
