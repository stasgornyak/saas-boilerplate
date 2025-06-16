<?php

namespace App\Services\_Common\Traits;

use App\Exceptions\ValidationException;
use Illuminate\Support\Facades\Validator;

trait HasValidation
{
    /**
     * @throws ValidationException
     */
    protected function validated(
        array $data,
        ?callable $validationRules = null,
        ?callable $validationData = null
    ): array {
        $data = $validationData ? $validationData($data) : $this->validationData($data);
        $rules = $validationRules ? $validationRules($data) : $this->validationRules($data);

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors()->all());
        }

        return $validator->validated();
    }

    protected function validationRules(array $data): array
    {
        return [];
    }

    protected function validationData(array $data): array
    {
        return $data;
    }
}
