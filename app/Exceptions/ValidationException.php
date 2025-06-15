<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

/**
 * For Validation Errors.
 *
 * Class ValidationException
 */
class ValidationException extends LogicException
{
    /**
     * ValidationException constructor.
     *
     * @param  string|array|null  $message
     */
    public function __construct($message = null, ?string $description = null)
    {
        $message = $message ?: MESSAGES['validation_errors'];

        parent::__construct($message, $description, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
