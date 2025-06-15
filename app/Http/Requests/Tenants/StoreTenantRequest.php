<?php

namespace App\Http\Requests\Tenants;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreTenantRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'settings' => ['array', 'nullable'],
            'avatar' => [
                'nullable',
                File::image()
                    ->dimensions(
                        Rule::dimensions()
                            ->maxWidth(MAX_IMAGE_DIMENSION)
                            ->maxHeight(MAX_IMAGE_DIMENSION)
                    )
                    ->max(MAX_IMAGE_SIZE),
            ],
        ];
    }
}
