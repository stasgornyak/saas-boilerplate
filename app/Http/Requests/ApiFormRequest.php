<?php

namespace App\Http\Requests;

use App\Exceptions\ValidationException;
use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ApiFormRequest extends FormRequest
{
    use SanitizesInput;

    public const PAGINATION_DEFAULTS = [
        'page' => 1,
        'per_page' => 50,
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function validateResolved(): void
    {
        $this->sanitize();

        parent::validateResolved();
    }

    /**
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();

        throw new ValidationException($errors);
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        return $this->transformAfterValidation($data);
    }

    protected function transformAfterValidation($data)
    {
        return $data;
    }

    protected function addDefaultPagination(array $data): array
    {
        if (! isset($data['pagination']['page'])) {
            $data['pagination']['page'] = self::PAGINATION_DEFAULTS['page'];
        }

        if (! isset($data['pagination']['per_page'])) {
            $data['pagination']['per_page'] = self::PAGINATION_DEFAULTS['per_page'];
        }

        return $data;
    }
}
